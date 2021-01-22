/* @var form форма с id="form" */

// загружаем файл на сервер
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
            success () {
                $.pjax.reload('#pjaxTableLoads')
            },
            error ({ status, responseJSON }) {
                switch (status) {
                    case 400:
                        alert(JSON.parse(responseJSON.message).join(', '))
                        break;
                    case 405:
                        alert(responseJSON.message)
                        break;
                }
            }
        })
    }
})

function remove(id) {
    $.ajax({
        url: `/delete?id=${id}`,
        type: 'get',
        data: false,
        contentType: false,
        cache: false,
        processData: false,
        success () {
            $.pjax.reload('#pjaxTableLoads')
        },
        error ({ status, responseJSON }) {
            switch (status) {
                case 404:
                    alert(responseJSON.message)
                    break;
            }
        }
    })
}