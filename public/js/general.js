$(document).ready(function() {
    $('.sidenav').sidenav();
    $('.collapsible').collapsible();
    $('select').formSelect();
    setTimeout(function(){ $('.input-field label').addClass('active'); }, 1);
    $('.dropdown-trigger').dropdown();
    $('.modal').modal();
    $('.tabs').tabs();
    $('.row-clickable').click(function () {
        if(this.hasAttribute('data-id')) {
            location.href = '/admin/lists/show/' + this.getAttribute('data-id');
        }
    });
    let elem = document.querySelector('.collapsible.expandable');
    let instance = M.Collapsible.init(elem, {
        accordion: false
    });
});