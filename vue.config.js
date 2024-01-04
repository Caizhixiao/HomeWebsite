const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  devServer:{
    host:'192.168.0.113',
    port:80,
  }
})
module.exports = {
  productionSourceMap: false,
}