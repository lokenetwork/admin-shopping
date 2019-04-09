// 设置图片合并的最小间隔
fis.config.set('settings.spriter.csssprites.margin', 20);

// 取消下面的注释开启simple插件，注意需要先进行插件安装 npm install -g fis-postpackager-simple
// fis.config.set('modules.postpackager', 'simple');

// 取消下面的注释设置打包规则
 fis.config.merge({
   roadmap:{
     path: [
       {
         reg:'**.css',
         useHash:true
       },
       {
         reg:'**.phtml',
         isHtmlLike:true
       }
     ]
   }

});

// 取消下面的注释可以开启simple对零散资源的自动合并
// fis.config.set('settings.postpackager.simple.autoCombine', true);