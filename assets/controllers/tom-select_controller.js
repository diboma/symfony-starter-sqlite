import { Controller } from '@hotwired/stimulus'
import TomSelect from 'tom-select'

export default class extends Controller {
  connect () {
    const tomSelectInstance = new TomSelect(this.element, {
      plugins: ['remove_button'],
      sortField: {
        field: 'text',
        direction: 'asc'
      }
    })

    const form = document.querySelector('[data-role=form-autocomplete-product]')
    form.addEventListener('submit', (e) => {
      e.preventDefault()
      const selectedValues = tomSelectInstance.getValue()
      console.log(selectedValues)

      for (const productId of selectedValues) {
        const productName = document.querySelector(`[data-option-id="${productId}"]`)?.dataset.productName
        console.log('name:', productName, '| id:', productId)
      }

      tomSelectInstance.clear()
    })
  }
}
