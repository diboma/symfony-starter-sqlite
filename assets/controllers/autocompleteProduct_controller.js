import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
  connect () {
    this.element.addEventListener('submit', (e) => {
      e.preventDefault()
      const formData = new FormData(this.element)

      for (const [key, value] of formData.entries()) {
        if (!key.includes('_token')) {
          console.log('productId:', value)
        }
      }
    })
  }
}
