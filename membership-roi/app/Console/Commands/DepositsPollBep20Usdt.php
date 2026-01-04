<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\DepositSession;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DepositsPollBep20Usdt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposits:poll {--minutes=120 : Look back N minutes for sessions.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll BscScan for BEP20 USDT deposits and credit wallets.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Prefer the existing bscscan config, but allow ETHERSCAN_* env as fallback
        // so deployments that only have an "Etherscan key" can still configure a V2 multichain base URL.
        $apiKey = (string) (config('services.bscscan.key')
            ?: env('BSCSCAN_API_KEY')
            ?: env('ETHERSCAN_API_KEY'));

        $baseUrl = (string) (config('services.bscscan.base')
            ?: env('BSCSCAN_API_BASE')
            ?: env('ETHERSCAN_API_BASE', 'https://api.bscscan.com/api'));

        $chainId = (int) (config('services.bscscan.chainid')
            ?: env('BSCSCAN_CHAIN_ID', 56)
            ?: env('BSC_CHAIN_ID', 56));
        $usdtContract = (string) config('services.bscscan.usdt_contract', env('USDT_BEP20_CONTRACT', '0x55d398326f99059fF775485246999027B3197955'));

        if (!$apiKey) {
            $this->error('Missing API key (set BSCSCAN_API_KEY or ETHERSCAN_API_KEY)');
            return Command::FAILURE;
        }

        // Detect V2 multichain endpoints (they require `chainid`).
        $isV2 = str_contains($baseUrl, '/v2/api');

        $lookbackMinutes = (int) $this->option('minutes');
        $since = now()->subMinutes(max(10, $lookbackMinutes));

        // Poll both active and recently-expired sessions so deposits can still be credited
        // even if the scheduler was delayed (as long as the tx happened within the reserved window).
        $sessions = DepositSession::query()
            ->whereIn('status', ['active', 'expired'])
            ->where('reserved_until', '>=', $since)
            ->with('depositAddress')
            ->orderBy('id')
            ->get();

        $creditedCount = 0;
        $detectedCount = 0;

        foreach ($sessions as $session) {
            $address = $session->depositAddress?->address;
            if (!$address) {
                continue;
            }

            $sessionExpired = $session->reserved_until->lessThanOrEqualTo(now());

            $query = [
                'module' => 'account',
                'action' => 'tokentx',
                // V2 multichain endpoints require chainid (BSC mainnet = 56).
                // V1 endpoints ignore unknown params, but we only add it when on /v2/api.
                ...($isV2 ? ['chainid' => $chainId] : []),
                'address' => $address,
                'contractaddress' => $usdtContract,
                'page' => 1,
                'offset' => 50,
                'sort' => 'desc',
                'apikey' => $apiKey,
            ];

            $resp = Http::timeout(20)->get($baseUrl, $query);

            if (!$resp->ok()) {
                $this->warn("BscScan request failed for {$address}: HTTP ".$resp->status());
                continue;
            }

            $json = $resp->json();
            if (!is_array($json) || ($json['status'] ?? null) !== '1') {
                // status 0 often means no transactions; log other failures to help debugging.
                $message = (string) (($json['message'] ?? '') ?: '');
                $result = $json['result'] ?? null;
                $resultStr = is_string($result) ? $result : '';
                if ($message && strtolower($message) !== 'no transactions found') {
                    $this->warn("BscScan returned status ".(($json['status'] ?? 'n/a'))." for {$address}: {$message} {$resultStr}");
                }
                continue;
            }

            $txs = $json['result'] ?? [];
            if (!is_array($txs)) {
                continue;
            }

            foreach ($txs as $tx) {
                $to = strtolower((string) ($tx['to'] ?? ''));
                $toExpected = strtolower($address);
                if ($to !== $toExpected) {
                    continue;
                }

                $hash = (string) ($tx['hash'] ?? '');
                if (!$hash) {
                    continue;
                }

                // Only credit deposits that happened within the session window.
                $ts = (int) ($tx['timeStamp'] ?? 0);
                if ($ts <= 0) {
                    continue;
                }
                $time = Carbon::createFromTimestampUTC($ts);
                if ($time->greaterThan($session->reserved_until)) {
                    continue;
                }

                // Skip already processed tx
                $exists = Deposit::query()->where('tx_hash', $hash)->exists();
                if ($exists) {
                    continue;
                }

                $tokenSymbol = (string) ($tx['tokenSymbol'] ?? 'USDT');
                $tokenDecimal = (int) ($tx['tokenDecimal'] ?? 18);
                $value = (string) ($tx['value'] ?? '0');
                if ($value === '' || $value === '0') {
                    continue;
                }

                // Convert to decimal amount with 2dp.
                $divisor = bcpow('10', (string) $tokenDecimal, 0);
                $amountRaw = bcdiv($value, $divisor, 8);
                $amount = bcadd($amountRaw, '0', 2);
                if (bccomp($amount, '0', 2) <= 0) {
                    continue;
                }

                $detectedCount++;

                DB::transaction(function () use ($session, $tx, $hash, $amount, $tokenSymbol, $tokenDecimal, $usdtContract, $time, &$creditedCount): void {
                    $deposit = Deposit::create([
                        'user_id' => $session->user_id,
                        'deposit_address_id' => $session->deposit_address_id,
                        'deposit_session_id' => $session->id,
                        'tx_hash' => $hash,
                        'block_number' => isset($tx['blockNumber']) ? (int) $tx['blockNumber'] : null,
                        'from_address' => $tx['from'] ?? null,
                        'to_address' => $tx['to'] ?? '',
                        'token_symbol' => $tokenSymbol,
                        'token_contract' => $usdtContract,
                        'token_decimals' => $tokenDecimal,
                        'amount' => $amount,
                        'status' => 'detected',
                        'detected_at' => $time,
                        'raw' => $tx,
                    ]);

                    // All deposits credit into the Registered Wallet.
                    $wallet = Wallet::forUser($session->user_id, Wallet::TYPE_REGISTERED);
                    $txRow = WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'type' => 'deposit_credit',
                        'amount' => $amount,
                        'meta' => [
                            'deposit_id' => $deposit->id,
                            'tx_hash' => $hash,
                            'from' => $tx['from'] ?? null,
                            'to' => $tx['to'] ?? null,
                        ],
                        'occurred_on' => $time->toDateString(),
                    ]);

                    $wallet->increment('balance', $amount);

                    $deposit->forceFill([
                        'status' => 'credited',
                        'credited_at' => now(),
                        'wallet_transaction_id' => $txRow->id,
                    ])->save();

                    $session->increment('credited_amount', $amount);

                    // Close session after first credited deposit (simple rule).
                    $session->forceFill([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ])->save();

                    $creditedCount++;
                });

                // Only one credit per session in this MVP.
                break;
            }

            // Mark session as expired after processing if the reserved window is over and it wasn't completed.
            if ($sessionExpired && $session->status === 'active') {
                $session->forceFill(['status' => 'expired'])->save();
            }
        }

        $this->info("Detected: {$detectedCount}, credited: {$creditedCount}");
        return Command::SUCCESS;
    }
}
