<?php
$github_token = getenv('PMU_GITHUB_TOKEN');
$git_clone = shell_exec("git clone https://$github_token@github.com/cperry-pantheon/pushback-test.git /tmp/client-site");
$copy_composer_files = shell_exec("cp composer.* /tmp/client-site");
$date = date('ymd');
$push_to_github = shell_exec("cd /tmp/client-site && git checkout -b pantheon-mu-$date && git add -A && git commit -m \"Pantheon Managed Updates: composer updates\" && git push origin pantheon-mu-$date");
$auth = base64_encode('pmu-ops' . ':' . $github_token);

// Initialize repo connection info.
// todo: make this configurable via yml?
$repo_name = 'pushback-test';
$owner_name = 'cperry-pantheon';
$site_name = 'pushback-test';
$base = 'master';
$head = 'pantheon-mu-'.$date;
$pr_api_url = "https://api.github.com/repos/$owner_name/$repo_name/pulls";
$pr_title = "[PMU] Updates for $site_name";

// Make PR from branch
$data = json_encode([
  'head' => $head,
  'base' => $base,
  'title' => $pr_title,
  // todo: add commit message to body maybe.
  'body' => '',
]);

$pr_creation = shell_exec("curl -X POST -H \"Accept: application/vnd.github.v3+json\" --header \"Authorization: Basic $auth\" $pr_api_url -d '$data'");

?>