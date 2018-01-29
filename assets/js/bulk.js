var Bulk = {
    initialize: function() {
        var checkAllBox = $('input[data-clanx-bulk-all-box]');
        if(checkAllBox){
            checkAllBox.click(function () {
                $('input[data-clanx-bulk-box="true"]').not(this).prop('checked', this.checked);
                // if there is one on the top and one on the bottom, also do the other one.
                $(checkAllSelector).not(this).prop('checked', this.checked);
            });
        }
    }
}

module.exports = Bulk;
