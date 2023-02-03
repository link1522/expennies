const ajax = async (url, method = 'get', data = {}, domElement = null) => {
  method = method.toLowerCase()

  const options = {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  }

  const csrfMethod = new Set(['post', 'put', 'delete', 'patch'])

  if (csrfMethod.has(method)) {
    options.body = JSON.stringify({ ...data, ...getCsrfFields() })
  } else if (method === 'get') {
    url += '?' + new URLSearchParams(data).toString()
  }

  const response = await fetch(url, options)

  if (domElement) {
    clearValidationErrors(domElement)
  }

  if (!response.ok) {
    if (response.status === 422) {
      const errors = await response.json()
      handleValidationErrors(errors, domElement)
    }
  }

  return response
}

const get = (url, data) => ajax(url, 'get', data)
const post = (url, data, domElement) => ajax(url, 'post', data, domElement)
const del = url => ajax(url, 'delete')

function handleValidationErrors(errors, domElement = document) {
  for (const name in errors) {
    const element = domElement.querySelector(`input[name="${name}"]`)

    element.classList.add('is-invalid')

    for (const error of errors[name]) {
      const errorDiv = document.createElement('div')

      errorDiv.classList.add('invalid-feedback')
      errorDiv.textContent = error

      element.parentNode.append(errorDiv)
    }
  }
}

function clearValidationErrors(domElement) {
  domElement.querySelectorAll('.is-invalid').forEach(element => {
    element.classList.remove('is-invalid')

    element.parentNode
      .querySelectorAll('.invalid-feedback')
      .forEach(element => element.remove())
  })
}

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

export { ajax, get, post, del }
