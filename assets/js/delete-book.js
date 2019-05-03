import axios from 'axios'

let deleteLinks = document.getElementsByClassName('js-delete-book')

for (let i = 0; i < deleteLinks.length; i++) {
    deleteLinks[i].addEventListener('click', deleteBook)
}

function deleteBook (e) {
    e.preventDefault()
    let path = this.dataset.path
    let bookId = this.dataset.bookId
    this.children[0].className = 'fas fa-spinner fa-spin'

    axios({
        method: 'delete',
        url: path,
    }).then(response => {
        document.getElementById('book-' + bookId).remove()
    }).catch(error => {
        this.children[0].className = 'fas fa-times'
    })

}