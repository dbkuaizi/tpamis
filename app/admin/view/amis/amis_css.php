<style>
/*  AMIS 主题 CSS 适配文件，根据不同的主题 调整路由*/
{switch $Think.config.amis.amis_theme}
{case antd}
    /* 仅 antd 主题加载 */
    /* 隐藏面包屑 */
    .amis-scope .antd-AppBcn { display: none; }
    /* header 下拉按钮样式 */
    .header-menu .antd-DropDown-menu li>a{color: #666;text-align: center;}
    .header-menu .antd-Button,.header-menu .antd-Button:hover{border:#fff;}
    .antd-Layout-body{position: relative}
    .antd-DropDown-menu{min-width: 100%;}
{/case}

{case ang}

    .amis-scope .header-menu .a-DropDown-menu li>a{color: #666;text-align: center;}
    .amis-scope .header-menu .a-Button,.amis-scope .header-menu .a-Button:hover{
        border:#fff;background-color: #fff;box-shadow:none;}
    .amis-scope .a-AppBcn { display: none; }
    .amis-scope .a-DropDown-menu{min-width: 100%;}

{/case}
{/switch}
/* 公共CSS 不管是什么主题 都会加载 */
html,body,.app-wrapper{position: relative;width: 100%;height:100%;margin: 0;padding: 0;}
/* 修复滚动条问题 */
.amis-scope{overflow-x: visible}
.a-DropDown-menu{min-width: 100%;}

</style>

