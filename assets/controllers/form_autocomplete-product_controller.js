import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
  connect () {
    this.element.addEventListener('submit', (e) => {
      e.preventDefault()

      const formData = new FormData(this.element)
      for (const [key, value] of formData.entries()) {
        if (!key.includes('_token')) {
          // console.log('productId:', value)
          const productName = document.querySelector(`option[value="${value}"]`)?.dataset.productName
          console.log('name:', productName, '| id:', value)
        }
      }

      const tomSelectInstance = new TomSelect(document.querySelector('[data-role=autocomplete-product]'))
      console.log('tomSelectInstance:', tomSelectInstance)
      const selectedValues = tomSelectInstance.getValue()
      console.log(selectedValues)

      for (const productId of selectedValues) {
        const productName = document.querySelector(`option[value="${productId}"]`)?.dataset.productName
        console.log('name:', productName, '| id:', productId)
      }

      tomSelectInstance.clear()
    })
  }
}
