#!/usr/bin/php
<?php
/*
# If this module saves you time, helps your clients, or helps you do better work, I’d appreciate some coffee:
# https://www.buymeacoffee.com/robwpdev
# Thanks! ~ Rob / PressWizards.com
*/
// Example usage to display last 3 months:
// php sk-proj-your-admin-key-here 3
// NOTE: MUST BE OPENAI ADMIN KEY NOT PROJECT KEY
if ($argc !== 3) {
    echo "Usage: php script.php <API_KEY> <MONTH_COUNT>\n";
    exit(1);
}

function getUsageDetails($apiKey, $monthCount) {
    if (empty($apiKey)) {
        echo "Error: API key is required.\n";
        exit(1);
    }

    if (empty($monthCount) || !is_numeric($monthCount) || $monthCount < 1 || $monthCount > 6) {
        echo "Error: Month count must be a number between 1 and 6.\n";
        exit(1);
    }

    // Calculate the date that is $monthCount months ago from today and the current time as UNIX timestamps
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $startDate = (clone $now)->modify("-$monthCount months")->setTime(0, 0)->getTimestamp();
    $currentTimestamp = $now->getTimestamp();

    $limit = min(30 * $monthCount, 180); // Ensure the limit does not exceed 180 days
    $nextPage = null;
    $totalCost = 0.0;

    $firstFormattedDate = date('m/d/y', $startDate);
    $lastFormattedDate = null;

    do {
        $url = "https://api.openai.com/v1/organization/costs"
             . "?start_time=$startDate&end_time=$currentTimestamp"
             . "&limit=$limit";

        if ($nextPage) {
            $url .= "&page=" . urlencode($nextPage);
        }

        $headers = [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Request Error: ' . curl_error($ch) . "\n";
            curl_close($ch);
            return;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            echo "HTTP Error: $httpCode\n";
            echo "Response: $response\n";
            return;
        }

        $usageData = json_decode($response, true);

        if (isset($usageData['error'])) {
            echo "API Error: " . $usageData['error']['message'] . "\n";
            return;
        }

        if (!is_array($usageData) || !isset($usageData['data'])) {
            echo "Unexpected API response format.\n";
            return;
        }

        // Process each bucket in the response
        foreach ($usageData['data'] as $bucket) {
            $bucketDate = date('m/d/y', $bucket['start_time']);
            $costForDay = 0.0;

            foreach ($bucket['results'] as $result) {
                $amount = floatval($result['amount']['value']);
                $currency = $result['amount']['currency'];
                $costForDay += $amount;
            }

            if ($costForDay > 0.0000001) { // Threshold to ignore negligible costs
                echo sprintf("Date: %s \$%.7f %s\n", $bucketDate, $costForDay, strtoupper($currency));
                $totalCost += $costForDay;
            }

            $lastFormattedDate = $bucketDate;
        }

        $nextPage = $usageData['next_page'] ?? null;

    } while ($nextPage);

    if ($lastFormattedDate) {
        echo sprintf("\nTotal Cost %s to %s: \$%.7f USD\n", $firstFormattedDate, $lastFormattedDate, $totalCost);
    }
}

// Example usage
if ($argc !== 3) {
    echo "Usage: php script.php <API_KEY> <MONTH_COUNT>\n";
    exit(1);
}

$apiKey = $argv[1];
$monthCount = (int)$argv[2];
getUsageDetails($apiKey, $monthCount);
