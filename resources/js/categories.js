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
    .addEventListener('click', function (event) {
      const categoryId = event.currentTarget.getAttribute('data-id')

      // TODO: Post update to the category
      console.log(categoryId)
    })
})

function openEditCategoryModal(modal, { id, name }) {
  const nameInput = modal._element.querySelector('input[name="name"]')

  nameInput.value = name

  modal._element.querySelector('.save-category-btn').setAttribute('data-id', id)

  modal.show()
}
