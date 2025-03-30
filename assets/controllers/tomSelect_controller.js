import { Controller } from '@hotwired/stimulus'
import TomSelect from 'tom-select'

export default class extends Controller {
  connect () {
    const selectElement = document.querySelector('[data-role=tomSelectProduct]')
    console.log('selectElement:', selectElement)

    if (selectElement) {
      new TomSelect(selectElement, {
        sortField: {
          field: 'text',
          direction: 'asc'
        }
      })
    }
  }
}
