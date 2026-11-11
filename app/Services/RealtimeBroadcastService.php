<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class RealtimeBroadcastService
{
    /**
     * Cache reachability check briefly within the current PHP process
     * so loops sending multiple notifications do not probe repeatedly.
     */
    protected static ?bool $reverbReachable = null;

    protected static float $checkedAt = 0.0;

    protected const CACHE_TTL_SECONDS = 2.0;

    /**
     * Broadcast an event only if the active broadcaster can actually accept it,
     * preventing multi-second TCP hang when local Reverb is offline.
     */
    public static function broadcastSafely(object $event, bool $toOthers = false): bool
    {
        $driver = (string) config('broadcasting.default', 'null');
        if ($driver === '' || $driver === 'null') {
            return false;
        }

        if ($driver === 'reverb' && !self::isReverbReachable()) {
            return false;
        }

        try {
            $pending = broadcast($event);
            if ($toOthers) {
                $pending->toOthers();
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Reverb broadcast skipped: ' . $e->getMessage());
            self::$reverbReachable = false;
            self::$checkedAt = microtime(true);

            return false;
        }
    }

    /**
     * Fast non-blocking TCP preflight check to the configured Reverb server.
     */
    public static function isReverbReachable(): bool
    {
        $now = microtime(true);
        if (self::$reverbReachable !== null && ($now - self::$checkedAt) < self::CACHE_TTL_SECONDS) {
            return self::$reverbReachable;
        }

        $host = trim((string) config('broadcasting.connections.reverb.options.host', '127.0.0.1'));
        $port = (int) config('broadcasting.connections.reverb.options.port', 8080);

        if ($host === '' || $port <= 0) {
            self::$reverbReachable = false;
            self::$checkedAt = $now;

            return false;
        }

        $isLocalhost = strtolower($host) === 'localhost';
        $isLoopback = $isLocalhost || in_array($host, ['127.0.0.1', '::1', '[::1]'], true);
        $probeHost = $isLocalhost ? '127.0.0.1' : $host;
        $timeout = $isLoopback ? 0.08 : 0.35;

        $fp = @stream_socket_client(
            "tcp://{$probeHost}:{$port}",
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT
        );

        if ($fp !== false) {
            fclose($fp);
            self::$reverbReachable = true;
            self::$checkedAt = microtime(true);

            return true;
        }

        // Fallback probe IPv6 loopback if host is 'localhost' and server binds ::1 only
        if ($isLocalhost) {
            $fp6 = @stream_socket_client(
                "tcp://[::1]:{$port}",
                $errno6,
                $errstr6,
                0.04,
                STREAM_CLIENT_CONNECT
            );

            if ($fp6 !== false) {
                fclose($fp6);
                self::$reverbReachable = true;
                self::$checkedAt = microtime(true);

                return true;
            }
        }

        self::$reverbReachable = false;
        self::$checkedAt = microtime(true);

        return false;
    }

    public static function resetCache(): void
    {
        self::$reverbReachable = null;
        self::$checkedAt = 0.0;
    }
}

