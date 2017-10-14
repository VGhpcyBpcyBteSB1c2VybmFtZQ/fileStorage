var theme = "grey";
var activePopup = [null, null];

$(".header").css({
	"width" : window.innerWidth+'px'
});

function validateFile()
{
	var files = $("input#file")[0].files;
	 if (files[0].size > (20*1024*1024))
	 {
    	window.location.href = "home.php?upload=limitExceeded";
    	return false;
    }
    return true;
}

function popup(id = activePopup[0], axis = activePopup[1], overlay = true)
{
	activePopup[0] = id;
	activePopup[1] = axis;
	var hider;

	if (overlay)
		hider = $("#overlay");
	else
		hider = $(null);
	var loginPage = $("#"+id);

	hX = (window.innerWidth/2);
	hY = (window.innerHeight/2);
	
	if (loginPage.attr('data-status') == 'false'){
		hider.show();

		if (axis == "")
		{
			loginPage.css({
				'transform':'translate(-50%, -50%) scale'+axis+'(1)',
				'left':hX+'px',
				'top':hY+'px'
			});
		}
		else
		{
			loginPage.css('transform', 'scale'+axis+'(1)')
		}

		loginPage.attr('data-status', 'true');
	}
	else{

		if (axis == "")
		{
			loginPage.css({
				'transform':'translate(-50%, -50%) scale'+axis+'(0)',
				'left':hX+'px',
				'top':hY+'px'
			});
		}
		else
		{
			loginPage.css('transform', 'scale'+axis+'(0)')
		}

		loginPage.attr('data-status', 'false');
		setTimeout(function(){
			hider.hide();
		}, 200);
	}
}

function validateRegister()
{
	var form = $("form[name='registerForm']");
	var fname = form.find("input[name='fname']").val();
	var lname = form.find("input[name='lname']").val();
	var email = form.find("input[name='email']").val();
	var valid = true;
	var regexp = /^[a-zA-Z]+$/;

	if (!regexp.test(fname) || !regexp.test(lname))
	{
		alert("Invalid Name! Name should contain only alphabets.");
		valid = false;
	}
	regexp = /^.+@.+(\.[a-zA-Z]{2,3})+$/;
	if (!regexp.test(email))
	{
		alert("Invalid Email! " + email);
		valid = false;
	}

	return valid;
}

function validateLogin()
{
	var form = $("form[name='loginForm']");
	var email = form.find("input[name='email']").val();
	var regexp = /^.+@.+(\.[a-zA-Z]{2,3})+$/;
	var valid = true;

	if (!regexp.test(email))
	{
		alert("Invalid Email! " + email);
		valid = false;
	}

	return valid;
}

$('input#file').change(function(){
    var files = $(this)[0].files;
    if(files.length > 0){
        $('div#uploaded').html(files.length+" file(s) selected");
    }else{
        $('div#uploaded').html("Choose file");
    }
});

////////////////////////////////////// CANVAS ////////////////////////////////////////////////////////////////////////////

if (!!document.getElementById('can'))
{
	var canvas = document.getElementById('can');
	var c = canvas.getContext('2d');

	canvas.height = window.innerHeight;
	canvas.width = window.innerWidth;

	var circles;
	var mx, my;

	function init()
	{
		circles = [];
		for (var i=0; i < 200; i++)
		{
			var dx = Math.random() * 4 - 2;
			var dy = Math.random() * 4 - 2;
			var radius = 2;
			var x = Math.random() * (window.innerWidth - 2*radius) + radius;
			var y = Math.random() * (window.innerHeight - 2*radius) + radius;

			circles.push(new Circle(x, y, radius, dx, dy));
		}
	}

	function getDistance(x1, y1, x2, y2)
	{
		return Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2);
	}

	window.addEventListener('resize', function()
	{
		canvas.height = window.innerHeight;
		canvas.width = window.innerWidth;
		init();
	});
	window.addEventListener("mousemove", function(event)
	{
		mx = event.x - 55;
		my = event.y - 25;
	});

	function Circle(x, y, radius, dx, dy)
	{
		this.x = x;
		this.y = y;
		this.radius = radius;
		this.dx = dx;
		this.dy = dy;
		this.color = "grey";

		this.draw = function()
		{
			c.beginPath();
			c.arc(this.x, this.y, this.radius, 0, Math.PI*2);
			c.fillStyle = this.color;
			c.fill();
		}

		this.update = function()
		{
			if (Math.abs(this.x - window.innerWidth) < this.radius || this.x < this.radius)
				this.dx *= -1;
			if (Math.abs(this.y - window.innerHeight) < this.radius || this.y < this.radius)
				this.dy *= -1;

			if (getDistance(this.x, this.y, mx, my) < 3600)
			{
				c.beginPath();
				c.moveTo(this.x, this.y);
				c.lineTo(mx, my);
				c.strokeStyle = theme;
				c.stroke();
			}

			this.x += this.dx;
			this.y += this.dy;

			this.draw();
		}

		this.connect = function()
		{
			var dist = [];
			for (var i=0; i < Math.floor(circles.length*0.8); i++)
			{
				dist.push(getDistance(this.x, this.y, circles[i].x, circles[i].y));
			}
			var i = dist.indexOf(Math.min(...dist));
			c.beginPath();
			c.moveTo(this.x, this.y);
			c.lineTo(circles[i].x, circles[i].y);
			c.strokeStyle = theme;
			c.stroke();
		}
	}

	function renderLoop()
	{
		requestAnimationFrame(renderLoop);
		c.clearRect(0, 0, window.innerWidth, window.innerHeight);

		for (var i = 0; i < circles.length; i++){
			circles[i].update();
			circles[i].connect();
		}
	}

	init();
	renderLoop();
}