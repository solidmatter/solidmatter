<?php

ini_set('output_buffering', 0);

echo '<pre>';
var_dump(apc_cache_info());
var_dump(apc_cache_info('user'));
echo '</pre>';

?>