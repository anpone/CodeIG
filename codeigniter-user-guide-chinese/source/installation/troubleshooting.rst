########
疑难解答
########

如果你发现无论输入什么 URL 都只显示默认页面的话，那么可能是你的服务器不支持 PATH_INFO 变量，该变量用来提供搜索引擎友好的 URL 。
解决这个问题的第一步是打开 application/config/config.php 文件，
找到 URI Protocol 信息，根据注释提示，该值可以有几种不同的设置方式，
你可以逐个尝试一下。
如果还是不起作用，你需要让 CodeIgniter 强制在你的 URL 中添加一个问号，
要做到这点，你可以打开 application/config/config.php 文件，
然后将下面的代码::

	$config['index_page'] = "index.php";

修改为这样::

	$config['index_page'] = "index.php?";

