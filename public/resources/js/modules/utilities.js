export default {
    initializeObjects: function(root) {
        let totalObject = {};
        $(`${root}`).each(function(i,e) {
            let href = $(this);
            totalObject[`${href.attr('name')}`] = $(this).val();
        });
        return totalObject;
    }
}