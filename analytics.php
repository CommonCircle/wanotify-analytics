<?php

$combine = array(
    "/cc-exposures-logo-green.png" => array("iOS installs"),
    "/wa-en-banner-green.png" => array("iOS installs")
);
$show = array(
"iOS installs",
// "/",
// "/uw",
// "/FAQ",
// "/privacy-policy",
"/next-steps-count",
"/next-steps-text",
"/next-steps-text-sp",
"/next-steps-count-30",
// "/next-steps",
// "/uw-next-steps",
// "/wanotify-ios-install.mobileconfig",
// "/robots.txt",
);

// subset of shown resources to bin by user agent
$by_user = array(
    "iOS installs",
    "/next-steps-count",
    "/next-steps-count-30",
);
$log_output = false;

$end_date = new DateTime();
// $start_date = date_create_from_format('Y-m-d H:i:s', '2020-11-01 00:00:00');
$start_date = (new DateTime('first day of this month'))->setTime(0,0);
$site_interval = 'daily';
$resource_interval = 'hourly'; // daily or hourly

$start_dt = new DateTime();
echo "Start time: {$start_dt->format('Y-m-d H:i:s')}\n";

$one_month = new DateInterval('P1M');
$curr_date = clone $start_date;
$log_files = array();
// $start_date = (new DateTime())->sub(new DateInterval('P15D'));

// find params
$log_base_folder = "/var/log/apache2";
$max_depth = 1;
$domain = 'wanotify*';
$search_string = "ssl-" . $domain . "_access*";
// get access log archives
while ($curr_date <= $end_date) {
    $log_folder = $log_base_folder."/".$curr_date->format('Y')."/".$curr_date->format('m');
    if (is_dir($log_folder)) {
        $log_files = array_merge($log_files, explode("\n", shell_exec("find $log_folder -maxdepth $max_depth -iname '$search_string'")));
    }
    $curr_date = $curr_date->add($one_month);
}
$log_files = array_merge($log_files, explode("\n", shell_exec("find $log_base_folder -maxdepth $max_depth -iname '$search_string'")));
// $log_files = array("./ssl-test.log"); // test
array_multisort($log_files);

if ($log_output) {

    $timestamp = $end_date->format('Y-m-d H:i:s');
    $output_folder = "./analytics_output";
    if (!file_exists($output_folder)) {
        mkdir($output_folder, 0777, true);
    }
    $txt_out = "$output_folder/analytics_$timestamp.txt";
    $txt_handle = fopen($txt_out, 'w');
    echo "Text output: $txt_out\n"; 
}

$resources = array();

 /*
 Visits to .html/no-extension resources
 count of users = # unique visitors
 [
    [
        'date' => yyyy-mm-dd,
        'visitors' => [],
        'views' => #,
        'hits' => #,
    ], ...
 ]
 */
$site_stats = array();

/*
 Hits on all resources and status codes
 [
    'page' => [
        'date' => yyyy-mm-dd,
        'hour' => hh:ii,
        'visitors' => [],
        'hits' => #,
    ], ...
 ]
 */
$resource_stats = array();

foreach ($log_files as $filename) {
    if ($filename) {
        echo "$filename\n";
        $isgz = strpos($filename, ".gz") !==false;
        $handle;
        $content;
        if ($isgz) {
            $handle = gzopen($filename, "r");
            $content = gzgets($handle, 4096);
        } else {
            $handle = fopen($filename, "r");
            $content = fgets($handle, 4096);
        }

        while ($content !== false) {
            $debug = false;
            // fwrite($txt_handle, "$content\n");
            // IP address
            preg_match("/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+) - - /", $content, $match); // '123.123.123.123 - - '
            $ip = $match[1] ?? "";
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // timestamp
            preg_match("/\[(.+?)\]/", $content, $match);
            $access_time = $match[1] ?? "";
            if ($access_time) {
                $datetime = date_create_from_format('d/M/Y:H+', $access_time);
            } else {
                continue;
            }
            // preg_match("/(\d{2})\/([A-Za-z]{3})\/(\d{4})/", $access_time, $date_match); // '19/Oct/2020'
            // $date = $date_match[0] ?? "";
            // preg_match("/(\d{2}):(\d{2}):(\d{2}).(\d{3})/", $access_time, $time_match); // 
            // $hour = $time_match[1] ?? "";
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // resource path
            preg_match("/\"[A-Z]{3,8} (.[^\"]+)\"/", $content, $match);
            $http = $match[1] ?? "";
            $parts = explode(" ", $http);
            $url = $parts[0];
            $page = page_basename($url);
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // status code
            preg_match("/([0-9]{3})/", $content, $match);
            $status_code = $match[1] ?? "";
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // referring url
            preg_match("/\"([^\"]+)\"/", $content, $match);
            $ref = $match[1] ?? "";
            $content = str_replace($match[0], "", $content);
            
            // fwrite($txt_handle, "$content\n");
            // browser
            preg_match("/\"([^\"]+)\"/", $content, $match);
            if ($match) {
                $browser = $match[1] ?? "";
                preg_match("/^[^\/]*/", $browser, $browser_match);
                $user_agent = $browser_match[0] ?? "";
                if (!$user_agent) {
                    $debug = true;
                }
                $content = str_replace($match[0], "", $content);
            }
            
            // fwrite($txt_handle, "$content\n");
            // bytes transferred
            // skip if no bytes sometimes prematurely removed from str
            preg_match("/([0-9]+\b|-)/", $content, $match);
            $bytes = 0;
            if ($match) {
                if (isset($match[1]) && $match[1] != "-") {
                    $bytes = $match[1];
                }
                $content = str_replace($match[0], "", $content);
            }
            if ((!$start_date || $datetime >= $start_date) && (!$end_date || $datetime <= $end_date)) {
                $date = $datetime->format('Y-m-d');
                $hour = $datetime->format('G:00:00');
                
                $aliases = array($page);
                if (isset($combine[$page])) {
                    $aliases = array_merge($aliases, $combine[$page]);
                }

                foreach ($aliases as $key) {
                    if (isset($resources[$key][$date][$ip][$status_code])) {
                        $resources[$key][$date][$ip][$status_code]++;
                    } else {
                        $resources[$key][$date][$ip][$status_code] = 1;
                    }

                    if (!isset($site_stats[$date])) {
                        $site_stats[$date] = array(
                            'time' => $date,
                            'hits' => 0,
                            'views' => 0,
                            'visitors' => array(),
                        );
                    }
                    $resource_ext = get_extension($key);
                    if ($status_code < 400 && (!$resource_ext || $resource_ext == 'html')) {
                        $site_stats[$date]['visitors'][$ip] = 1;
                        $site_stats[$date]['views']++;
                    }
                    $site_stats[$date]['hits']++;

                    if (empty($show) || in_array($key, $show)) {
                        $time_key = $date;
                        if ($resource_interval == 'hourly') {
                            $time_key .= " $hour";
                        }

                        if (!isset($resource_stats[$key][$time_key])) {
                            $resource_stats[$key][$time_key] = array(
                                'resource' => $key,
                                'time' => $time_key,
                                'hits' => 0,
                                'visitors' => array(),
                                // status codes added below
                            );
                        }
                        if (!isset($resource_stats[$key][$time_key][$status_code])) {
                            $resource_stats[$key][$time_key][$status_code] = 0;
                        }
                        $resource_stats[$key][$time_key][$status_code]++;

                        $resource_stats[$key][$time_key]['visitors'][$ip] = 1;

                        $resource_stats[$key][$time_key]['hits']++;

                        if (in_array($key, $by_user) && $user_agent) {
                            // $key .= " $user_agent";
                            if (!isset($resource_stats[$key]['user_agents'][$user_agent][$time_key])) {
                                $resource_stats[$key]['user_agents'][$user_agent][$time_key] = array(
                                    'resource' => $key,
                                    'user_agent' => $user_agent,
                                    'time' => $time_key,
                                    'hits' => 0,
                                    'visitors' => array(),
                                    // status codes added below
                                );
                            }
                            if (!isset($resource_stats[$key]['user_agents'][$user_agent][$time_key][$status_code])) {
                                $resource_stats[$key]['user_agents'][$user_agent][$time_key][$status_code] = 0;
                            }
                            $resource_stats[$key]['user_agents'][$user_agent][$time_key][$status_code]++;
    
                            $resource_stats[$key]['user_agents'][$user_agent][$time_key]['visitors'][$ip] = 1;
    
                            $resource_stats[$key]['user_agents'][$user_agent][$time_key]['hits']++;
                        }
                    }
                }
            }
            
            if ($debug && $log_output) {
                fwrite($txt_handle, "Row: i\n$content\nIP: $ip\nTime: $access_time\nPage: $page\nType: link[1]\nCode: $status_code\nBytes: $bytes\nRef: $ref\nBrowser: $browser");
            }

            if ($isgz) {
                $content = gzgets($handle, 4096);
            } else {
                $content = fgets($handle, 4096);
            }
        }
        
        if ($isgz) {
            gzclose($handle);
        } else {
            fclose($handle);
        }
    }
}

//==== d3 Outputs ===============================

// Format and flatten daily site stats for d3
$site_out = "./site_stats_$site_interval.json";
$site_handle = fopen($site_out, 'w');
echo "Site data: $site_out\n";
foreach ($site_stats as $day => $stats) {
    $site_stats[$day]['visitors'] = sizeof($stats['visitors']);
}
$site_stats = array_values($site_stats);
fwrite($site_handle, json_encode($site_stats, JSON_PRETTY_PRINT));

// // Format and flatten hourly resource stats for d3
// $resource_stats_flat = array();
// foreach ($resource_stats as $resource => $time_stats) {
//     foreach ($time_stats as $time => $stats) {
//         $resource_stats[$resource][$time]['visitors'] = sizeof($stats['visitors']);
//     }
//     $resource_stats_flat = array_merge($resource_stats_flat, array_values($resource_stats[$resource]));
// }
$resource_data = array(
    'start_time' => $start_date->format('Y-m-d G:i:s'),
    'end_time' => $end_date->format('Y-m-d G:i:s'),
    'data' => $resource_stats
);
$resource_out = "./resource_stats_$resource_interval.json";
$resource_handle = fopen($resource_out, 'w');
echo "Resource data: $resource_out\n";
fwrite($resource_handle, json_encode($resource_data, JSON_PRETTY_PRINT));

//===============================================

//=== log output ================================
if ($log_output) {
    // Write complete daily counts to timestamped file
    foreach ($resources as $page => $dates) {
        fwrite($txt_handle, "Page: $page\n");
        foreach ($dates as $date => $ips) {
            fwrite($txt_handle, "\tDate: $date\n");
            $hit_total = 0;
            $codecount = array();
            foreach ($ips as $ip => $status_codes) {
                foreach ($status_codes as $code => $count) {
                    $hit_total += $count;
                    if (!isset($codecount[$code])) {
                        $codecount[$code] = 0;
                    }
                    $codecount[$code] += $count;
                }
            }
            $unique = sizeof($ips);
            fwrite($txt_handle, "\tUnique Visitors: $unique\n");
            fwrite($txt_handle, "\tHits: $hit_total\n");
            fwrite($txt_handle, "\tStatus Codes:\n");
            foreach ($codecount as $code => $count) {
                fwrite($txt_handle, "\t\t$code: $count\n");
            }
        }
        fwrite($txt_handle, "\n");
    }
}
//===============================================

$end_dt = new DateTime();
echo "End time: {$end_dt->format('Y-m-d H:i:s')}\n";
if ($log_output) {
    fclose($txt_handle);
}
fclose($site_handle);
fclose($resource_handle);

//=== function defs =============================
function date_in_range($date_str, $start_date=null, $stop_date=null) {
    $date = date_create_from_format('d/M/Y', $date_str);
    $track = !(isset($start_date) && $date < $start_date || isset($stop_date) && $date > $stop_date);
    return $track;
}

function page_basename($url) {
    preg_match("/^[^\?]*/", $url, $match);
    $no_params = $match[0] ?? "";
    if (strpos($no_params, ".html")) {
        $no_params = substr_replace($no_params, "", -5);
    }
    if ($no_params == "/index") {
        $no_params = "/";
    }
    return $no_params;
}

function get_extension($url) {
    preg_match("/.*\.(.+?)$/", $url, $match);
    if (isset($match[1])) {
        return strtolower($match[1]);
    }
    return false;
}
?>
