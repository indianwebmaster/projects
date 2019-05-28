/* Animate fireworks in a DIV class/id tag passed */
/* Key variables:
    Speed of an explosion: explosion_speed = 25 - 75 ms
    Time between explosions: time_between_explosions = 1000 - 2000 ms
    Duration of fireworks: (in html file, fireworks_animation_duration (30000ms (30s) to 3600000 ms (1h))

    number_of_sparks (100 to 1000)
    radius_of_explosion (5 to 50)

    speed_of_sparks_falling (0.25 to 3.0)
    start_sparkling_count () 10000 to 50000)
    size_of_explosion () 5000 to 50000)
    gravity_force () 10000000 to 15000000 - higher number == lower gravity.)
    particle_fade_factor () 0.97 - 0.99)
*/

function getRandomFloat (min, max) {
    var randomFloat = ( Math.random() * (max - min) ) + min;
    return randomFloat;
}

function getRandomInt (min, max) {
    return Math.floor(getRandomFloat (min, max));
}

function animate(selector) {
    var $canvas = $(selector);
    var width = $canvas.innerWidth();
    var height = $canvas.innerHeight();

    var fireworksFactory = function fireworksFactory() {
        var centerX = (0.2 + 0.6 * Math.random()) * width;
        var centerY = (0.1 + 0.4 * Math.random()) * height;
        var color = new Color(2 * Math.PI * Math.random(), Math.random(), 0.9);
        return new Firework(centerX, centerY, color);
    };

    var fireworks = [fireworksFactory()];
    var animation = new Animation($canvas, fireworks, fireworksFactory);
    animation.start();
    return animation;
}

function fillBanner(selector, bannerText) {
    $(selector).text(bannerText);
}


function Animation($canvas, objects, factory) {
    this.canvas = $canvas.get(0);
    this.canvasContext = this.canvas.getContext('2d');
    this.objects = objects;
    this.factory = factory;
}

Animation.prototype.start = function start() {
    var canvas = this.canvas;
    var context = this.canvasContext;
    var objects = this.objects;
    var factory = this.factory;

    var explosion_speed = 50;   // ms
    var time_between_explosions = 2000;     //ms

    var redraw = function redraw() {
        context.clearRect(0, 0, canvas.width, canvas.height);
        for (var f = objects.length - 1; f >= 0; f--) {
            var particles = objects[f].particles;
            for (var p = particles.length - 1; p >= 0; p--) {
                var particle = particles[p];
                context.beginPath();
                context.arc(particle.x, particle.y, particle.size, 0, 2 * Math.PI, false);
                context.fillStyle = particle.color;
                context.fill();
            }
            objects[f].update();
        }
    };

    var launch = function launch() {
        objects.push(factory());
        while (objects.length > 4) {
            objects.shift();
        }
    };

    this.redrawInterval = setInterval(redraw, explosion_speed /* ms */);
    this.factoryInterval = setInterval(launch, time_between_explosions /* ms */);
}

Animation.prototype.stop = function stop() {
    clearInterval(this.factoryInterval);    // This will stop any new explosions
    setTimeout(function() { clearInterval(this.redrawInterval); }, 3000);   // This will clear the last explosion after 3 seconds
}


function Firework(centerX, centerY, color) {
    var number_of_sparks = getRandomInt(100,1000);  // 100 to 1000
    var radius_of_explosion = getRandomInt(5,50);   // 5 to 50

    this.centerX = centerX;
    this.centerY = centerY;
    this.color = color;
    this.particles = new Array(number_of_sparks);
    this.Δr = radius_of_explosion;
    this.age = 0;   // Offset from center. Simulates the sparks falling
    this.color = color

    var τ = 2 * Math.PI;
    for (var i = 0; i < this.particles.length; i++) {
        this.particles[i] = new Particle(
            this.centerX, this.centerY,
            /* r= */ 0, /* θ= */ τ * Math.random(), /* φ= */ τ * Math.random(),
            /* size= */ 2, color.rgb()
        );
    }
}

Firework.prototype.update = function update() {
    var start_sparkling_count = getRandomInt(10000,50000); // 10000 to 50000
    var size_of_explosion = getRandomInt(5000,50000);      // 5000 to 50000

    var speed_of_sparks_falling = 1.0;  // 0.25 to 3.0
    var gravity_force = 12500000;       // 10000000 to 15000000 - higher number == lower gravity.
    var particle_fade_factor = 0.98;    // 0.97 - 0.99

    for (var i = 0; i < this.particles.length; i++) {
        this.particles[i].r += this.Δr;
        this.particles[i].recalcCartesianProjection();

        this.Δr -= (this.Δr * this.Δr)/size_of_explosion ;            // Air resist
        this.particles[i].y += (this.age * this.age)/gravity_force;   // Gravity
        this.particles[i].size *= particle_fade_factor;               // Fade
        this.age+=speed_of_sparks_falling;
        if(this.age > start_sparkling_count){
            // Let the particles sparkle after some time
            this.particles[i].color = this.color.rgba();
        }
    }
};


function Color(hue, saturation, lightness) {
    /* Based on https://en.wikipedia.org/wiki/HSL_and_HSV#From_HSL */
    /* hue ∈ [0, 2π), saturation ∈ [0, 1], lightness ∈ [0, 1] */
    var c = (1 - Math.abs(2 * lightness - 1)) * saturation;
    var h = 3 * hue / Math.PI;
    var x = c * (1 - (h % 2 - 1));
    var r1 = (h < 1 || 5 <= h) ? c
           : (h < 2 || 4 <= h) ? x
           : 0;
    var g1 = (1 <= h && h < 3) ? c
           : (h < 4) ? x
           : 0;
    var b1 = (3 <= h && h < 5) ? c
           : (2 <= h) ? x
           : 0;
    var m = lightness - c / 2;
    var r = Math.floor(256 * (r1 + m));
    var g = Math.floor(256 * (g1 + m));
    var b = Math.floor(256 * (b1 + m));
    /*
    console.log('hsl(' + hue + ', ' + saturation + ', ' + lightness +
                ') = rgb(' + r + ', ' + g + ', ' + b + ')');
    */
    this.r = r;
    this.g = g;
    this.b = b;
}

Color.prototype.rgb = function() {
    return 'rgb(' + this.r + ', ' + this.g + ', ' + this.b + ')';
};

Color.prototype.rgba = function() {
    var opacity = Math.min(1, Math.random()*5);
    return 'rgba(' + this.r + ', ' + this.g + ', ' + this.b + ', ' + opacity + ')';
};

//////////////////////////////////////////////////////////////////////

function Particle(x, y, r, θ, φ, size, color) {
    this.origX = x;
    this.origY = y;
    this.r = r;
    this.sinθ = Math.sin(θ);

    this.sinφ = Math.sin(φ);
    this.cosφ = Math.cos(φ);
    this.size = size;
    this.color = color;
    this.recalcCartesianProjection();
}

Particle.prototype.recalcCartesianProjection = function() {
    this.x = this.origX + this.r * this.sinθ * this.cosφ;
    this.y = this.origY + this.r * this.sinθ * this.sinφ;
};
