<?php

$content = file_get_contents('phpunit.xml');
$content = str_replace('<env name="APP_ENV" value="testing"/>', '<env name="APP_ENV" value="testing"/>'."\n        <env name=\"APP_KEY\" value=\"base64:wIBy9tE7d6tqN8jO4G3Fh/dCx9gBxzJ+zP9lW4F8m5Y=\"/>", $content);
file_put_contents('phpunit.xml', $content);
