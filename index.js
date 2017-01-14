$(function(){
  var myform = new Vue({
    el: '#myform',
    data: {
      title: 'Nginx Config Generator',
      forms:{
        server_name: "www.example.com",
        root_path: "/var/www/html",
        use_php: true,
        use_ssl: false
      },
      result: ''
    },
    methods: {
      generate: function(){
        var _this = this;
        $.ajax({
          type: "GET",
          url: 'genconfig.php',
          data: {
            server_name: _this.forms.server_name,
            root_path: _this.forms.root_path,
            ssl: _this.forms.use_ssl,
            php: _this.forms.use_php,
            security_header: _this.forms.security_header,
            big_data: _this.forms.big_data
          },
          timeout: 3000
        }).done(function(data){
          _this.result = data;
        }).fail(function(data){
          console.log('error');
        });
      }
    },
    created: function(){ this.generate()}
  });
  myform.$watch('forms', function(now, prev) {
    myform.generate();
  },{
    deep: true
  });
});