function clamp(n, min, max) {
    return Math.min(Math.max(n, min), max)
}

function randomNumberBetween(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min)
}

class PuzzleCaptcha extends HTMLElement
{
    connectedCallback() {
        const width = parseInt(this.getAttribute('width'), 10)
        const height = parseInt(this.getAttribute('height'), 10)
        const pieceWidth = parseInt(this.getAttribute('piece-width'), 10)
        const pieceHeight = parseInt(this.getAttribute('piece-height'), 10)
        const maxX = width - pieceWidth
        const maxY = height - pieceHeight
        const input = this.querySelector('.captcha-anwser')

        let position = {x: randomNumberBetween(0, maxX), y: randomNumberBetween(0, maxY)}

        this.classList.add('captcha')
        this.classList.add('captcha-waiting-interaction')

        this.style.setProperty('--width', `${width}px`)
        this.style.setProperty('--height', `${height}px`)
        this.style.setProperty('--piece-width', `${pieceWidth}px`)
        this.style.setProperty('--piece-height', `${pieceHeight}px`)
        this.style.setProperty('--image', `url(${this.getAttribute('src')})`)

        const piece = document.createElement('div')
        piece.classList.add('captcha-piece')
        // init piece transform because position is random now
        piece.style.setProperty('transform', `translate(${position.x}px, ${position.y}px)`)

        this.append(piece)

        let isDragging = false;
        piece.addEventListener('pointerdown', e => {
            isDragging = true
            document.body.style.setProperty('user-select', 'none')
            this.classList.remove('captcha-waiting-interaction')
            piece.classList.add('is-moving')

            window.addEventListener('pointerup', () => {
                isDragging = false
                document.body.style.removeProperty('user-select')
                piece.classList.remove('is-moving')
                input.value = `${position.x}-${position.y}`

            }, {once: true})
        })

        this.addEventListener('pointermove', e => {
            if (!isDragging) {
                return;
            }

            position.x = clamp(position.x + e.movementX, 0, maxX)
            position.y = clamp(position.y + e.movementY, 0, maxY)

            piece.style.setProperty('transform', `translate(${position.x}px, ${position.y}px)`)
        })
    }
}

customElements.define('puzzle-captcha', PuzzleCaptcha)