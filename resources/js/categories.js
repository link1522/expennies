import '../css/categories.scss'
import { Modal } from 'bootstrap'

window.addEventListener('DOMContentLoaded', function () {
  const editCategoryModal = new Modal(
    document.getElementById('editCategoryModal')
  )

  document.querySelectorAll('.edit-category-btn').forEach(button => {
    button.addEventListener('click', async function (event) {
      const categoryId = event.currentTarget.getAttribute('data-id')

      const response = await fetch(`/categories/${categoryId}`)
      const data = await response.json()

      openEditCategoryModal(editCategoryModal, data)
    })
  })

  document
    .querySelector('.save-category-btn')
    .addEventListener('click', async function (event) {
      const categoryId = event.currentTarget.getAttribute('data-id')

      const response = await fetch(`/categories/${categoryId}`, {
        method: 'POST',
        body: JSON.stringify({
          name: editCategoryModal._element.querySelector('input[name="name"]')
            .value,
          ...getCsrfFields()
        }),
        headers: {
          'Content-Type': 'application/json'
        }
      })
      const data = await response.json()
    })
})

function getCsrfFields() {
  const csrfNameMeta = document.querySelector('#csrfName')
  const csrfValueMeta = document.querySelector('#csrfValue')

  const csrfNameKey = csrfNameMeta.getAttribute('name')
  const csrfName = csrfNameMeta.content
  const csrfValueKey = csrfValueMeta.getAttribute('name')
  const csrfValue = csrfValueMeta.content

  return {
    [csrfNameKey]: csrfName,
    [csrfValueKey]: csrfValue
  }
}

function openEditCategoryModal(modal, { id, name }) {
  const nameInput = modal._element.querySelector('input[name="name"]')

  nameInput.value = name

  modal._element.querySelector('.save-category-btn').setAttribute('data-id', id)

  modal.show()
}
