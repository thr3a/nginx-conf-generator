<?php

function h($param = ''){
  return filter_input(INPUT_GET, $param, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

function flag($param = ''){
  return filter_input(INPUT_GET, $param, FILTER_VALIDATE_BOOLEAN);
}

$base = <<< EOL
  listen {{PORT}};
  server_name {{SERVER_NAME}};
  
  # Root Path
  root {{ROOT_PATH}};
  index {{INDEX}};
    
EOL;

$base = str_replace("{{SERVER_NAME}}", h('server_name'), $base);
$base = str_replace("{{ROOT_PATH}}", h('root_path'), $base);

if (flag('ssl')) {
  $base = str_replace("{{PORT}}", '443', $base);
$base .= <<< EOL
  
  # SSL
  ssl on;
  ssl_certificate /etc/letsencrypt/live/www.example.com/fullchain.pem;
  ssl_certificate_key /etc/letsencrypt/live/www.example.com/privkey.pem;
  
EOL;
}else {
  $base = str_replace("{{PORT}}", '80', $base);
}

if (flag('php')) {
$base .= <<< 'EOL'
  
  # PHP
  location ~ \.php$ {
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_pass unix:/var/run/php7.1-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
  }
  
EOL;
  $base = str_replace("{{INDEX}}", 'index.html index.php', $base);
}else {
  $base = str_replace("{{INDEX}}", 'index.html', $base);
}

if (flag('security_header')) {
$base .= <<< EOL
  
  # Security headers
  add_header X-Content-Type-Options nosniff;
  add_header X-Frame-Options "SAMEORIGIN";
  add_header X-XSS-Protection "1; mode=block";
  
EOL;
}

if (flag('big_data')) {
$base .= <<< EOL
  
  client_max_body_size 1G;
  fastcgi_buffers 64 4K;
  
EOL;
}
?>
<?= "server {\r$base\r}" ?>