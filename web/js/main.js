$(document).on('click', '#send', function () {
    let formData = new FormData(form)

    if (form.elements[0].files.length === 0) {
        alert('Загрузите файл')
    } else {
        $.ajax({
            url: '/convert',
            type: 'post',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success (response) {
                console.log(response)
            }
        })
    }
})