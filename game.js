// game.js ausgelagerte logik für das spiel

document.addEventListener('DOMContentLoaded', () => {
    const gameContainer = document.getElementById('game-container');
    const crystalArea = document.getElementById('crystal-area');
    const timeDisplay = document.getElementById('time-display');
    const scoreDisplay = document.getElementById('score-display');
    const levelDisplay = document.getElementById('level-display');
    const crystalsDisplay = document.getElementById('crystals-display');
    const progressFill = document.getElementById('progress-fill');
    
    const gameOverMessage = document.getElementById('game-over-message');
    const gameWonMessage = document.getElementById('game-won-message');

    let timeLeft = parseInt(timeDisplay.textContent, 10);
    let gameRunning = !gameOverMessage.style.display.includes('block') && !gameWonMessage.style.display.includes('block');
    let timerInterval;

    function updateHUD(gameState) {
        scoreDisplay.textContent = new Intl.NumberFormat().format(gameState.score);
        levelDisplay.textContent = gameState.level;
        crystalsDisplay.textContent = `${gameState.crystals_found}/${gameState.crystals_needed}`;
        
        const percentage = (gameState.crystals_found / gameState.crystals_needed) * 100;
        progressFill.style.width = `${percentage}%`;
        
        timeLeft = gameState.time_left;
        timeDisplay.textContent = `${timeLeft}s`;

        gameRunning = !gameState.game_over && !gameState.game_won;
        
        if (gameState.game_over) {
            document.getElementById('final-score-lost').textContent = new Intl.NumberFormat().format(gameState.score);
            document.getElementById('final-level-lost').textContent = gameState.level;
            gameOverMessage.style.display = 'block';
            stopTimer();
        }
        
        if (gameState.game_won) {
            document.getElementById('final-score-won').textContent = new Intl.NumberFormat().format(gameState.score);
            gameWonMessage.style.display = 'block';
            stopTimer();
        }
    }
    
    function renderCrystals(crystals) {
        crystalArea.innerHTML = '';
        crystals.forEach(crystal => {
            if (!crystal.found) {
                const button = document.createElement('button');
                button.className = 'crystal';
                button.dataset.crystalId = crystal.id;
                button.style.position = 'absolute';
                button.style.left = `${crystal.x}px`;
                button.style.top = `${crystal.y}px`;
                button.style.background = 'none';
                button.style.border = 'none';
                button.style.padding = '0';
                
                const div = document.createElement('div');
                div.style.width = '30px';
                div.style.height = '30px';
                div.style.background = 'radial-gradient(circle, var(--primary-cyan), var(--primary-orange))';
                div.style.borderRadius = '50%';
                div.style.border = '2px solid var(--border-gold)';
                div.style.boxShadow = '0 0 15px var(--shadow-glow)';
                
                button.appendChild(div);
                crystalArea.appendChild(button);
            }
        });
    }

    async function sendRequest(action, data = {}) {
        const formData = new FormData();
        formData.append('action', action);
        for (const key in data) {
            formData.append(key, data[key]);
        }

        try {
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API Request failed:', error);
            // Fallback: Seite neu laden, um den Zustand zu synchronisieren
            window.location.reload();
        }
    }

    crystalArea.addEventListener('click', async (e) => {
        const crystalButton = e.target.closest('.crystal');
        if (!crystalButton || !gameRunning) {
            return;
        }
        
        crystalButton.classList.add('collecting');
        const crystalId = crystalButton.dataset.crystalId;
        
        const newState = await sendRequest('collect_crystal', { crystal_id: crystalId });
        
        if (newState) {
            const isLevelUp = parseInt(levelDisplay.textContent) < newState.level;
            updateHUD(newState);
            if (isLevelUp) {
                // Bei Level-Up Kristalle neu rendern
                renderCrystals(newState.crystals);
            } else {
                 crystalButton.remove();
            }
        }
    });
    
    document.querySelectorAll('#reset-button-lost, #reset-button-won, #reset-button-main').forEach(button => {
        button.addEventListener('click', async () => {
            const newState = await sendRequest('reset_game');
            if (newState) {
                gameOverMessage.style.display = 'none';
                gameWonMessage.style.display = 'none';
                updateHUD(newState);
                renderCrystals(newState.crystals);
                startTimer();
            }
        });
    });

    function startTimer() {
        if (timerInterval) clearInterval(timerInterval);
        if (!gameRunning) return;
        
        timerInterval = setInterval(() => {
            if (timeLeft > 0) {
                timeLeft--;
                timeDisplay.textContent = `${timeLeft}s`;
                if (timeLeft <= 10) {
                    timeDisplay.classList.add('time-warning');
                } else {
                    timeDisplay.classList.remove('time-warning');
                }
            } else {
                stopTimer();
                gameRunning = false;
                sendRequest('reset_game').then(state => {
                    state.game_over = true;
                    updateHUD(state);
                });
            }
        }, 1000);
    }
    
    function stopTimer() {
        clearInterval(timerInterval);
    }

    if (gameRunning) {
        startTimer();
    }
});