<?php

namespace App\Modules\Submissions\Helpers;

class ExecutionConfigHelper
{
    public static function getLimits(?string $langName = null): array
    {
        $getConfigValue = function (string $key, $default) use ($langName) {
            if ($langName) {
                $value = config("submissions.judge.$langName.$key");
                if ($value !== null) {
                    return $value;
                }
            }

            $value = config("submissions.judge.$key");
            return $value !== null ? $value : $default;
        };

        return [
            'cpu_limit' => $getConfigValue('cpu_limit', '0.5'),
            'memory_limit' => $getConfigValue('memory_limit', '128m'),
            'time_limit_seconds' => $getConfigValue('time_limit_seconds', 2),
        ];
    }
}
