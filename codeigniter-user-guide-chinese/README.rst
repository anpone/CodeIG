.. image:: https://travis-ci.org/CodeIgniter-Chinese/codeigniter-user-guide.svg?branch=master
    :target: https://travis-ci.org/CodeIgniter-Chinese/codeigniter-user-guide

########################
CodeIgniter 3.0 中文手册
########################

这里是 CodeIgniter 3.0 的中文手册，翻译自 CodeIgniter `官方的英文手册 <http://www.codeigniter.com/user_guide/index.html>`_ ，
以 `aneasystone <http://www.aneasystone.com/>`_ 翻译的手册为基础制作。

如果想修改或翻译本手册，请在 Github 上提交 Issues ，或者 Fork 本项目作出修改，然后提交 Pull Request ，
感谢大家为手册做出的贡献。

********
安装步骤
********

CodeIgniter 的用户指南是使用 Sphinx 软件进行管理，并可以生成各种不同的格式。
所有的页面都是采用 `ReStructured Text <http://sphinx.pocoo.org/rest.html>`_
格式书写，这种格式非常方便人们阅读。

安装条件
========

Sphinx 软件依赖于 Python，如果你使用的是 OS X 系统，则系统已经自带 Python 了。
你可以在终端中执行不带参数的 ``python`` 命令，以确认你的系统是否已安装 Python 。
如果你已安装，会显示出你当前所使用的版本。
如果显示的不是2.7以上版本，你可以去这里下载并安装2.7.2
http://python.org/download/releases/2.7.2/

安装
====

1. 安装 `easy_install <http://peak.telecommunity.com/DevCenter/EasyInstall#installing-easy-install>`_
2. ``easy_install "sphinx==1.2.3"``
3. ``easy_install sphinxcontrib-phpdomain``
4. 安装 CI Lexer，它可以高亮文档中的 PHP, HTML, CSS, 和 JavaScript 代码 (参见 *cilexer/README*)
5. 返回代码库根目录
6. ``make html``

译注：

1. Ubuntu 系统上安装 easy_install 可以直接：``sudo apt-get install python-setuptools``
2. easy_install 需要 root 权限，前面加上 sudo

编辑并创建文档
==============

所有的源文件都在 *source/* 目录下，在这里你可以添加新的文档或修改已有的文档。
如同代码的修改一样，我们推荐从你的 feature 分支发起 pull request 到这里的 *develop* 分支。

那么，HTML 文档在哪里？
=======================

很显然，HTML 文档才是我们最关心的，因为这毕竟才是用户最终看到的。
由于对自动生成的文件进行版本控制没有意义，所以它们并不在版本控制之下。
你如果想要预览 HTML 文档，你可以重新生成它们。生成 HTML 文档非常简单，
首先进入你的用户指南目录，然后执行上面安装步骤中的最后一步::

	make html

你将会看到正在编译中的信息，编译成功后，生成的用户指南和图片都位于 *build/html/* 目录下。
在 HTML 第一次编译之后，后面将只会针对修改的文件进行重编译，这将大大的节约我们的时间。
如果你想再重新全部编译一次，只需删除 *build* 目录然后编译即可。

不想自己编译？
==============

如果不想自己编译，那么可以直接 `在线访问 <http://codeigniter-chinese.github.io/codeigniter-user-guide/>`_ 、 `打包下载 <http://codeigniter-chinese.github.io/codeigniter-user-guide/codeigniter_user_guide.zip>`_ 或者 `下载PDF版 <http://codeigniter-chinese.github.io/codeigniter-user-guide/CodeIgniter.pdf>`_。


********
风格指南
********

使用 Sphinx 为 CodeIgniter 编写文档，请参考 source/documentation/index.rst 的一般准则。
