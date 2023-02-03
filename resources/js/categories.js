import '../css/categories.scss'
import { Modal } from 'bootstrap'
import { get, post, del } from './ajax'

window.addEventListener('DOMContentLoaded', function () {
  const editCategoryModal = new Modal(
    document.getElementById('editCategoryModal')
  )

  document.querySelectorAll('.edit-category-btn').forEach(button => {
    button.addEventListener('click', async function (event) {
      const categoryId = event.currentTarget.getAttribute('data-id')

      const response = await get(`/categories/${categoryId}`)
      const data = await response.json()

      openEditCategoryModal(editCategoryModal, data)
    })
  })

  document
    .querySelector('.save-category-btn')
    .addEventListener('click', async function (event) {
      const categoryId = event.currentTarget.getAttribute('data-id')

      const response = await post(
        `/categories/${categoryId}`,
        {
          name: editCategoryModal._element.querySelector('input[name="name"]')
            .value
        },
        editCategoryModal._element
      )

      if (response.ok) {
        editCategoryModal.hide()
      }
    })

  document
    .querySelector('.delete-category-btn')
    .addEventListener('click', async function (event) {
      const categoryId = event.currentTarget.getAttribute('data-id')

      if (confirm('Are you sure you want to delete this category?')) {
        await del(`/categories/${categoryId}`)
      }
    })
})

function openEditCategoryModal(modal, { id, name }) {
  const nameInput = modal._element.querySelector('input[name="name"]')

  nameInput.value = name

  modal._element.querySelector('.save-category-btn').setAttribute('data-id', id)

  modal.show()
}
