  <h1 align="center">
    <img src="./images/logo.svg" align="center">
  </h1>
  <p align="center">
    <b>基于 ThinkPHP 8.0 与 amis 构建的低代码开发框架</b>
  </p> 
<p align="center">
 <a href="#">
    <img src="https://img.shields.io/badge/PHP-8.2%2B-blue" alt="Build Status">
 </a>
 <a href="#">
    <img src="https://img.shields.io/badge/MySQL-5.7%2B-orange" alt="Build Status">
 </a>
 <a href="#">
    <img src="https://img.shields.io/badge/ThinkPHP-8.1.2-green.svg" alt="Build Status">
 </a>
 <a href="#">
    <img src="https://img.shields.io/badge/AMIS-2.0.2-blue.svg" alt="Build Status">
 </a>
 <a href="#">
 <img src="https://img.shields.io/badge/license-Apache--2.0-red" alt="Build Status">
 </a>
</p>

该项目可以通过纯 JSON 快速配置后台管理系统，无需前端参与，后端开发人员即可完成。
严格来说依然属于 MVC 架构，并非前后端分离。

## 特点

- 无需专业前端，纯 JSON 构建页面，支持 amis 120+ 的前端组件。
- 移动端兼容，amis 提供了良好的移动端设备兼容，无需额外开发移动端页面。
- 常用后台功能，提供了完整的后台基础功能，开箱即用。
- Echarts 图表，支持 Echarts 所有功能。

## 功能

- [x] 组件管理，组件 JSON 管理，支持嵌套使用
- [x] 字典管理，枚举映射、动态配置
- [x] 接口管理，使用SQL可直接查询列表、单条、树形、option 四种数据结构
- [x] 通用CURD, 使用通用接口快速对数据进行简单的增删改查操作 (仅支持单表，多表或复杂操作仍需要编写接口代码)
- [x] 通用排序, 通用排序接口开发
- [x] 扩展模板，扩展 Tpamis 特有的模板标签，这些标签可以帮我们更好的构建页面 JSON
- [x] 用户管理，后端的用户管理功能
- [x] 个人中心，后端用户可以自行重置密码及基础信息修改
- [x] 角色管理，用户权限控制
- [x] 菜单管理，对后台菜单进行配置
- [x] 主题切换，通过修改配置文件，即可使用 amis 提供的主题样式
- [x] 日志管理，后台用户的登录日志和操作日志
- [x] 附件管理，通用的附件上传接口，以及附件管理功能

## 文档（完善中）
https://gitee.com/dbkuaizi/tpamis/wikis

## 部署

要求：PHP 7.2.5+，MySQL 5.7+

1. `git clone` 克隆项目
2. `composer install` 初始化 `vender`
3. 导入项目根目录 `tpamis.sql` SQL 文件，要求数据库版本不低于 MySQL 5.7 / MariaDB 10.1
4. 配置 `.env` 数据库链接信息
5. 配置本地php环境、虚拟主机之类 （每个人用的环境不同，就是加一个站点）
6. 访问登录页面，账号：`admin` 密码：`admin123`

有问题可以提 issues，看到就会回复，但不保证时效。

## 开发

如果你真的觉得这套东西还凑合，打算用来做一些项目的话，你可能需要了解一下这些：

- ThinkPHP6 的使用方法。[参考文档](https://www.kancloud.cn/manual/thinkphp6_0)
- Amis 的基础概念，实际开发的时候照着文档复制粘贴就行。[参考文档](https://aisuda.bce.baidu.com/amis/docs) 

可以通过我的这篇文章：[《基于 amis 的一些尝试》](https://www.dbkuaizi.com/archives/amis.html)，参考是否适合你的需求。
 
## 截图

| 模块 | PC端 | 移动端 |
| ---- | ---- | ---- |
| 登录 | ![登录](./images/login.png) | ![登录](./images/m_login.png) |
| 首页 | ![首页](./images/index.png) | ![首页](./images/m_index.png) |
| 字典管理 | ![字典](./images/map.png) | ![字典](./images/m_map.png)  |
| 接口管理 | ![接口](./images/api.png) | ![接口](./images/m_api.png)  |
| 菜单管理 | ![菜单](./images/menu.png) | ![菜单](./images/m_menu.png) |
| 角色管理 | ![角色](./images/role.png) | ![角色](./images/m_role.png) |
| 日志管理 | ![日志](./images/log.png) | ![日志](./images/m_log.png)|
| 个人中心 | ![个人中心](./images/userinfo.png) | ![个人中心](./images/m_userinfo.png) |

## 作者
- 作者：两双筷子
- 博客：https://www.dbkuaizi.com

## 版权
Tpamis 使用了与 ThinkPHP 和 amis 一致的版权协议 Apache2。
本项目可以免费商用但必须在代码中标注。