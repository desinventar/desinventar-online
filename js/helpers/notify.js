import { Notyf } from 'notyf'
import 'notyf/notyf.min.css'

function success(message) {
  const notyf = new Notyf({ position: { x: 'right', y: 'top' } })
  notyf.success(message)
}

function error(message) {
  const notyf = new Notyf({ position: { x: 'right', y: 'top' } })
  notyf.error(message)
}

export default {
  success,
  error
}
