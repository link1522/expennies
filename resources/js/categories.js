import '../css/categories.scss'
import { Modal } from 'bootstrap'
import { get, post } from './ajax'

window.addEventListener('DOMContentLoaded', function () {
  const editCategoryModal = new Modal(
    document.getElementById('editCategoryModal')
  )

  document.querySelectorAll('.edit-category-btn').forEach(button => {
    button.addEventListener('click', async function (event) {
      const categoryId = event.currentTarget.getAttribute('data-id')

      const data = await get(`/categories/${categoryId}`)

      openEditCategoryModal(editCategoryModal, data)
    })
  })

  document
    .querySelector('.save-category-btn')
    .addEventListener('click', async function (event) {
      const categoryId = event.currentTarget.getAttribute('data-id')

      const data = await post(`/categories/${categoryId}`, {
        name: editCategoryModal._element.querySelector('input[name="name"]')
          .value
      })

      console.log(data)
    })
})

function openEditCategoryModal(modal, { id, name }) {
  const nameInput = modal._element.querySelector('input[name="name"]')

  nameInput.value = name

  modal._element.querySelector('.save-category-btn').setAttribute('data-id', id)

  modal.show()
}
