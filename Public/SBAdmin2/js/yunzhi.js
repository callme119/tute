jQuery.extend(jQuery.validator.messages, {
    required: "内容不能为空",
	remote: "请修正该字段",
	email: "请输入正确格式的电子邮件",
	url: "请输入合法的网址",
	date: "请输入合法的日期",
	dateISO: "请输入合法的日期 (ISO).",
	number: "请输入合法的数字",
	digits: "只能输入整数",
	creditcard: "请输入合法的信用卡号",
	equalTo: "请再次输入相同的值",
	accept: "请输入拥有合法后缀名的字符串",
	maxlength: jQuery.validator.format("请输入一个 长度最多是 {0} 的字符串"),
	minlength: jQuery.validator.format("请输入一个 长度最少是 {0} 的字符串"),
	rangelength: jQuery.validator.format("请输入 一个长度介于 {0} 和 {1} 之间的字符串"),
	range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
	max: jQuery.validator.format("请输入一个最大为{0} 的值"),
	min: jQuery.validator.format("请输入一个最小为{0} 的值")
});

//handlebar 判断helper
Handlebars.registerHelper('compare', function(left, operator, right, options) {
	if (arguments.length < 3) {
		throw new Error('Handlerbars Helper "compare" needs 2 parameters');
	}
	var operators = {
		'==':     function(l, r) {return l == r; },
		'===':    function(l, r) {return l === r; },
		'!=':     function(l, r) {return l != r; },
		'!==':    function(l, r) {return l !== r; },
		'<':      function(l, r) {return l < r; },
		'>':      function(l, r) {return l > r; },
		'<=':     function(l, r) {return l <= r; },
		'>=':     function(l, r) {return l >= r; },
		'typeof': function(l, r) {return typeof l == r; }
	};

	if (!operators[operator]) {
		throw new Error('Handlerbars Helper "compare" doesn\'t know the operator ' + operator);
	}

	var result = operators[operator](left, right);

	if (result) {
		return options.fn(this);
	} else {
		return options.inverse(this);
	}
});

//日期插件
var dataInit = function(){
	$('.date').datetimepicker({
        language:  'zh-CN',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,		//默认视图。2－month.
		minView: 2,			//默认提供的最精确的视图 1-day
		maxView: 4,			//向上点选时，最多能提供前后5年的选择
		forceParse: 0,
		format: 'yyyy-mm-dd'
    });
};

//定义trim去空格函数
String.prototype.trim = function()
{
    return this.replace(/(^[\\s]*)|([\\s]*$)/g, "");
}

//日期插件初始化
$(document).ready(function(){
	dataInit();
	$(".select2").select2();
	$(document).on("click",".delete",function(event){
		$this = $(this);
		//堵塞默认事件
		event.preventDefault();
		alertify.set({ labels: { ok: "确认", cancel: "取消" } });
		alertify.confirm("梦云智 提醒您: 此操作不可逆，请您再次确认", function (e) {
			if (e) {
				location.href = $this.attr("href");
				return false;
			} else {
				alertify.error("操作已取消");
				return false;
			}
		});
	});

	
});
