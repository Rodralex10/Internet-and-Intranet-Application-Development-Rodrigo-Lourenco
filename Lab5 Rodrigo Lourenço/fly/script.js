class Fly {
    constructor(type = 'normal') {
        this.type = type;
        this.scoreValue = type === 'normal' ? 1 : 2;
        this.width = 40;
        this.height = 40;
        this.x = Math.random() * (window.innerWidth - this.width);
        this.y = Math.random() * (window.innerHeight - this.height);
        this.direction = Math.random() * Math.PI * 2;
        this.speed = type === 'normal' ? (1 + Math.random() * 2) : (2 + Math.random() * 3);
        this.isActive = false;

        this.element = document.createElement('div');
        this.element.className = `fly ${type}`;
        this.element.style.width = `${this.width}px`;
        this.element.style.height = `${this.height}px`;
        document.body.appendChild(this.element);
    }

    move() {
        if (!this.isActive) return;

        this.x += Math.cos(this.direction) * this.speed;
        this.y += Math.sin(this.direction) * this.speed;

        if (this.x <= 0 || this.x >= window.innerWidth - this.width) {
            this.direction = Math.PI - this.direction;
            this.x = Math.max(0, Math.min(this.x, window.innerWidth - this.width));
        }
        if (this.y <= 0 || this.y >= window.innerHeight - this.height) {
            this.direction = -this.direction;
            this.y = Math.max(0, Math.min(this.y, window.innerHeight - this.height));
        }

        this.element.style.left = `${this.x}px`;
        this.element.style.top = `${this.y}px`;

        if (Math.random() < 0.01) {
            this.direction += (Math.random() - 0.5) * Math.PI / 2;
        }

        requestAnimationFrame(() => this.move());
    }

    show() {
        this.isActive = true;
        this.x = Math.random() * (window.innerWidth - this.width);
        this.y = Math.random() * (window.innerHeight - this.height);
        this.element.style.left = `${this.x}px`;
        this.element.style.top = `${this.y}px`;
        this.element.style.display = 'block';
        this.move();
    }

    hide() {
        this.isActive = false;
        this.element.style.display = 'none';
    }

    onClick(callback) {
        this.element.addEventListener('click', () => {
            if (this.isActive) {
                callback(this);
                this.element.style.transform = 'scale(1.3) rotate(15deg)';
                setTimeout(() => {
                    this.element.style.transform = 'scale(1) rotate(0)';
                }, 200);
            }
        });
    }
}

class Game {
    constructor() {
        this.score = 0;
        this.timeLeft = 30;
        this.level = 1;
        this.gameActive = false;
        this.flies = [];
        this.gameTimer = null;
        this.levelTargets = [10, 25, 50, 100, 200, 400, 800];
        this.highscore = localStorage.getItem('highscore') || 0;

        this.setupUI();
        this.createFlies();
        this.setupEventListeners();
    }

    setupUI() {
        this.scoreElement = document.getElementById('score');
        this.timerElement = document.getElementById('timer');
        this.levelElement = document.getElementById('level');
        this.startButton = document.getElementById('start');
        this.resetButton = document.getElementById('reset');
        this.finalScoreElement = document.getElementById('final-score');
        this.finalLevelElement = document.getElementById('final-level');
        this.gameOverElement = document.getElementById('game-over');
        this.playAgainButton = document.getElementById('play-again');
        this.recordElement = document.getElementById('highscore');
        this.recordElement.textContent = this.highscore;
    }

    createFlies() {
        for (let i = 0; i < 3; i++) {
            this.flies.push(new Fly('normal'));
        }
        this.flies.push(new Fly('special'));

        this.flies.forEach(fly => {
            fly.onClick((clickedFly) => this.handleFlyClick(clickedFly));
        });
    }

    setupEventListeners() {
        this.startButton.addEventListener('click', () => this.startGame());
        this.playAgainButton.addEventListener('click', () => {
            this.gameOverElement.style.display = 'none';
            this.startGame();
        });
        this.resetButton.addEventListener('click', () => {
            this.endGame();
            this.resetGame();
        });
    }

    startGame() {
        if (this.gameActive) return;

        this.resetGame();
        this.gameActive = true;

        this.flies.forEach(fly => fly.show());
        this.gameTimer = setInterval(() => this.updateTimer(), 1000);
    }

    updateTimer() {
        this.timeLeft--;
        this.timerElement.textContent = this.timeLeft;

        if (this.timeLeft <= 0) {
            this.endGame();
        }
    }

    handleFlyClick(fly) {
        this.score += fly.scoreValue;
        this.scoreElement.textContent = this.score;
        fly.direction = Math.random() * Math.PI * 2;
        this.checkLevelCompletion();
    }

    checkLevelCompletion() {
        if (this.level <= this.levelTargets.length &&
            this.score >= this.levelTargets[this.level - 1]) {
            this.levelUp();
        }
    }

    levelUp() {
        this.level++;
        this.levelElement.textContent = this.level;
        this.timeLeft += 10; // Add 10 seconds when leveling up
        this.timerElement.textContent = this.timeLeft;

        this.flies.forEach(fly => {
            fly.speed *= 1.2;
        });

        // add nf ou gf when LUP!
        const newFlyType = Math.random() > 0.5 ? 'normal' : 'golden';
        const newFly = new Fly(newFlyType);
        newFly.onClick((clickedFly) => this.handleFlyClick(clickedFly));
        this.flies.push(newFly);
        if (this.gameActive) newFly.show();
    }

    endGame() {
        this.gameActive = false;
        clearInterval(this.gameTimer);

        if (this.score > this.highscore) {
            this.highscore = this.score;
            localStorage.setItem('highscore', this.highscore);
            this.recordElement.textContent = this.highscore;
        }

        this.flies.forEach(fly => fly.hide());

        this.finalScoreElement.textContent = this.score;
        this.finalLevelElement.textContent = this.level;
        this.gameOverElement.style.display = 'block';
    }

    resetGame() {
        this.score = 0;
        this.timeLeft = 30;
        this.level = 1;
        this.scoreElement.textContent = this.score;
        this.timerElement.textContent = this.timeLeft;
        this.levelElement.textContent = this.level;

        // Remove all flies w gf
        this.flies.forEach(fly => fly.hide());
        this.flies = [];
        this.createFlies();
    }

    changeBgColor(color) {
        document.documentElement.style.setProperty('--background-color', color);
    }
}

let game;
window.onload = () => {
    game = new Game();
};
