<?php
$pregrid = '<div class="grid" id="grid" data-masonry=\'{ "itemSelector": ".grid-item"}\'>';
$postgrid = '</div>';
$predata = '<div class="grid-item">';
$postdata = '</div>';
$tw_script = '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script><script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>';
$final_feed = '';
$feed_ar_to_str = '';

$embeds = array(
	'<blockquote class="twitter-tweet" data-lang="en"><p lang="en" dir="ltr">redwhiteandpewpew with get_repost<br>・・・<a href="https://twitter.com/BergaraUSA?ref_src=twsrc%5Etfw">@BergaraUSA</a> lineup at the range on Sunday with…<a href="https://t.co/lHwxlAl1i2">https://t.co/lHwxlAl1i2</a></p>&mdash; Bergara USA (@BergaraUSA)<a href="https://twitter.com/BergaraUSA/status/971541195552935936?ref_src=twsrc%5Etfw">March 8, 2018</a></blockquote>',
	'<blockquote class="twitter-tweet" data-lang="en"><p lang="en" dir="ltr">redwhiteandpewpew with get_repost<br>・・・<a href="https://twitter.com/BergaraUSA?ref_src=twsrc%5Etfw">@BergaraUSA</a> lineup at the range on Sunday with…<a href="https://t.co/lHwxlAl1i2">https://t.co/lHwxlAl1i2</a></p>&mdash; Bergara USA (@BergaraUSA)<a href="https://twitter.com/BergaraUSA/status/971541195552935936?ref_src=twsrc%5Etfw">March 8, 2018</a></blockquote>'
);
$final_feed .= $pregrid;

foreach($embeds as $html) {
	$feed_ar_to_str .= $predata . $html . $postdata;
}
$final_feed .=  $feed_ar_to_str . $postgrid . $tw_script;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
	<?php echo $final_feed ?>
</body>
</html>