<img src="./public/static/logo.png" align="center">

  <h1 align="center">
    Tpamis
  </h1>
  <h4 align="center">
    基于 ThinkPHP6.0 与 amis 构建的低代码开发框架
  </h4> 
<p align="center">
 <a href="#">
    <img src="https://img.shields.io/badge/PHP-7.2%2B-blue" alt="Build Status">
 </a>
 <a href="#">
    <img src="https://img.shields.io/badge/MySQL-5.7%2B-orange" alt="Build Status">
 </a>
 <a href="#">
    <img src="https://img.shields.io/badge/ThinkPHP-6.0.10-green.svg" alt="Build Status">
 </a>
 <a href="#">
    <img src="https://img.shields.io/badge/AMIS-1.6.0-blue.svg" alt="Build Status">
 </a>
 <a href="#">
 <img src="https://img.shields.io/badge/license-Apache--2.0-red" alt="Build Status">
 </a>
</p>



该项目可以通过纯 JSON 快速配置后台管理系统，无需前端参与，后端开发人员即可完成。
严格来说依然属于 MVC 架构，并非前后端分离。

## 功能
- [x] 组件管理，组件 JSON 管理，支持嵌套使用
- [x] 字典管理，枚举映射、动态配置
- [x] 接口管理，使用SQL可直接查询列表、单条、树形、option 四种数据结构
- [x] 通用CURD, 使用通用接口快速对数据进行简单的增删改查操作 (仅支持单表，多表或复杂操作仍需要编写接口代码)
- [x] 通用排序, 通用排序接口开发
- [x] 扩展模板，扩展 Tpamis 特有的模板标签，这些标签可以帮我们更好的构建页面 JSON
- [x] 用户管理，后端的用户管理功能
- [ ] 角色管理，用户权限控制
- [ ] 菜单管理，对后台菜单进行配置
- [x] 主题切换，通过修改配置文件，即可使用 amis 提供的主题样式
- [ ] 日志管理，后台用户的登录日志和操作日志

## 版权信息
Tpamis 使用了与 ThinkPHP 和 amis 一致的的版权协议 Apache2。
本项目可以免费商用但必须在代码中标注。