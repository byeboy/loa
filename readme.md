#面向工建企业的协同办公系统（后台API）

##项目概述
* **项目名称：**面向工建企业的协同办公系统
* **项目分析：**简单的协同办公（即OA）系统
* **项目特性：**集成仓储管理功能，实现浅度即时通信
* **项目预期：**前端采用[dva](https://github.com/dvajs/dva)框架，引用[ANT DESIGN](https://ant.design/index-cn)组件实现单页应用效果；后台采用[lumen](https://lumen.laravel.com/)框架，前后对接时数据格式统一为`JSON`
* **项目环境：**后台开发环境为`nginx`、`php`、`mysql`

***

##项目思路
本项目拟采用V层分离设计方案，后台`API`尽量向`RESTful`靠拢，对于即时通讯拟采用`WebSocket`协议实现，用户认证拟采用`token`令牌实现。旨在现将该项目放至`GitHub`，在学习实践过程中熟悉`GitHub`，欢迎大牛批评指正，在此谢过。

###Todo
- [x] 职员管理功能
  - [x] 注册
  - [x] 登录
  - [x] 增删改查
- [x] 公告管理功能
  - [x] 增删改查
- [x] 任务管理功能
  - [x] 增删改查
  - [ ] 代码优化
- [x] 部门管理功能
  - [x] 增删改查
- [ ] 仓储管理功能
  - [ ] 增删改查
  - [ ] 仓储统计
  - [ ] 数据导出
- [ ] 文件处理功能
  - [ ] 文件上传
    - [ ] 图像上传
    - [ ] 文档上传
  - [ ] 文件下载

***
> In me the tiger sniffs the rose.

—— *by Siegfried Sassoon*, **In Me, Past, Present, Future Meet**



***
# 附录（Lumen简介）
## Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/lumen-framework/v/unstable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

### Official Documentation
Documentation for the framework can be found on the [Lumen website](http://lumen.laravel.com/docs).

### Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

### License

The Lumen framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
