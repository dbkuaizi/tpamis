ThinkAmis
===============
<p align="center">
 <img src="https://img.shields.io/badge/PHP-7.2%2B-blue" alt="Build Status">
 <img src="https://img.shields.io/badge/ThinkPHP-6.0.9-green.svg" alt="Build Status">
 <img src="https://img.shields.io/badge/AMIS-1.4.41-blue.svg" alt="Build Status">
 <img src="https://img.shields.io/badge/license-Apache--2.0-red" alt="Build Status">
</p>

是基于 [ThinkPHP6.0](https://gitee.com/liu21st/thinkphp) 与 百度开源项目 [amis](https://gitee.com/baidu/amis) 构建的后端管理系统

该项目可以通过纯 JSON 快速配置后台管理系统，无需前端参与，后端开发人员即可完成。
严格来说依然属于 MVC 架构，并非前后端分离。

## 功能
- [x] 组件管理，组件 JSON 管理，支持嵌套使用
- [ ] 字典管理，枚举映射
- [ ] 接口管理，使用SQL查询，代替大部分数据接口
- [x] 扩展模板，扩展 ThinkAmis 特有的模板标签，这些标签可以帮我们更好的构建页面 JSON
- [ ] 用户管理，后台用户管理
- [ ] 角色管理，用户权限控制（目前仅菜单控制）
- [ ] 菜单管理，对后台菜单进行配置
- [ ] 主题切换，通过修改配置文件，即可使用 amis 提供的主题样式
- [ ] 日志管理，后台用户的登录日志和操作日志

## 版权信息
ThinkAmis 使用了与 ThinkPHP 和 amis 一致的的版权协议 Apache2，本项目可以免费商用但必须在代码中标注。