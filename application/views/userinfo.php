<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
	<title>userinfo</title>
</head>
<body>
	<?php foreach($userinfo as $key => $v){?>
		<p> <?php echo $key.':'.$v;?></p>
	<?php }?>
</body>
</html>