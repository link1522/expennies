const ajax = (url, method = 'get', data = {}) => {
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

  return fetch(url, options).then(response => response.json())
}

const get = (url, data) => ajax(url, 'get', data)
const post = (url, data) => ajax(url, 'post', data)

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

export { ajax, get, post }
