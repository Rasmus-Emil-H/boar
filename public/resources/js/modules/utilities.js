export default {
    initializeObjects: function(root) {
        let totalObject = {};
        $(`${root}`).each(function(i,e) {
            let href = $(this);
            totalObject[href.data('id')] = {};
            $(this).find('input').each(function(i,e) {
                totalObject[`${href.data('id')}`][`${$(this).attr('name')}`] = $(this).val();
            });
        });
        return totalObject;
    }
}