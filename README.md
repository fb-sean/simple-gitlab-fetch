# simple-gitlab-fetch
A simple PHP script to use the GitLab API


```php
$api = new GitlabAPI('xxx');
$file = $api->getFile(11111, 'configs%2Fconfig.json');

var_dump($file);
```

Format the lines

```php
$api = new GitlabAPI('xxx');
$file = $api->getFile(11111, 'configs%2Fconfig.json');

$formatted = GitlabAPI::convertLinesToString($file['lines']);

echo $formatted;
```
