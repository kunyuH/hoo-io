<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>hoo-vue3</title>
    <script src="https://cdn.staticfile.net/vue/3.0.5/vue.global.js"></script>
</head>
<body>
    <div id="hello-vue" class="demo">
        ${message}
    </div>



    <script>
        const HelloVueApp = {
            data() {
                return {
                    message: 'Hello Vue!!'
                }
            }
        }
        const app = Vue.createApp(HelloVueApp)

        app.config.compilerOptions.delimiters = ['${', '}'];

        app.mount('#hello-vue')
    </script>
</body>
</html>
