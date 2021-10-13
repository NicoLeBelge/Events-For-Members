class IconSet {
    /**
     * @param {Element} nest 
     * @param {Array} whole_set 
     * @param {Array} init_set 
     * @param {String} init_set
     * @param {Boolean} dynamic 
     *
     * builds a set of n div (n<100), each containing the string of whole_set[0] ... whole_set[n-1]
     * if div kth div contains a string listed in [init_set], then div has class E4M_on. Otherwize, class E4M_off
     * divs have id prefix_id01, prefix_id02, prefix_idnn, 
     * if init_set is ["*"] then all divs have class "E4M_on"
     * if dynamic is true, clicking the div calls Toggle_on_off_class, which must be provided elswhere
     * Status returns the string to be stored in the database for updated set ("*" if all ON or all OFF).
     * 
     * required from external :
     * - css definition for E4M_on and E4M_off classes
     * - Toggle_on_off_class function
     * 
     */
    constructor(nest, whole_set, init_set, prefix_id, dynamic){
        this.nest = nest;
        this.whole_set = whole_set;
        this.actual_set = init_set;
        this.prefix_id = prefix_id;
        this.dynamic = dynamic;
        this.Build();
    }
    Status(){
        let coll = document.getElementById(this.nest).children;
        let status_str='';
        let all_on = true;
        let all_off = true;
        for ( let i = 0; i < coll.length ; i++ ) {
            if (coll[i].classList.contains("E4M_on")) {
                status_str += "'" + this.whole_set[i] +"', ";
                all_off &&= false;
            } else {
                all_on &&= false;
            }
        }
        status_str = status_str.substring(0, status_str.length-2);
        if (all_on || all_off){
            status_str = '"*"';
        }
        return status_str;
    }
    Build() {
        let     nesting_div = document.getElementById(this.nest);
        for ( let i = 0; i < this.whole_set.length ; i++ ) {
            let newdiv = document.createElement('div');
            newdiv.innerHTML = this.whole_set[i];
            if (this.actual_set.includes(this.whole_set[i]) || this.actual_set[0] == "*") {
                newdiv.classList.add("E4M_on");
            } else {
                newdiv.classList.add("E4M_off");
            };
            newdiv.id = this.prefix_id + i.toString(10).padStart(2,'0');
            if (this.dynamic) {
                newdiv.addEventListener('click', Toggle_on_off_class);
            };
            nesting_div.appendChild(newdiv);
        }
    }
    Refresh (newset) {
        this.actual_set = newset;
        document.getElementById(this.nest).innerHTML="";
        this.Build();
    }   
}
class Selector{
    /**
     * @param {Element} nest 
     * @param {number} nbDIV
     * @param {number} active
     * @param {function} callback
     * @param {string} plus_url
     *
     * builds a set of nbDIV div, each containing the string 1, 2, ... n
     * each div has onclick calling the function callback. 
     * The function callback can find argument in event.currentTarget.callback_arg (0, 1, ..., nbDIV-1)
     * div of order active (1 <= active <= n) has css class E4M_on, others have E4M_off
     * if plus_url is provided, a supplementary div (marked +, class E4M_off) leads to plus_url 
     * Update method delete existing selector, and rebuild with new_active E4M_on
     * required from external : css for each div
     */
   
    constructor(nest, nbDIV, active, callback, plus_url){
        this.nest = nest;
        this.nbDIV = nbDIV;
        this.active = active;
        this.callback = callback;
        this.plus_url = plus_url;
        this.Build();
    }
    Update(new_active){
        let nesting_div = document.getElementById(this.nest);
        nesting_div.innerHTML="";
        this.active = new_active;
        this.Build();
    }
    Build(){
        let nesting_div = document.getElementById(this.nest);
        for ( let i = 0; i < this.nbDIV ; i++ ) {
            let newdiv = document.createElement('div');
            newdiv.innerHTML = "<p>" + (i+1).toString(10) + "</p>";
            //newdiv.innerHTML = (i+1).toString(10) ;
            if (i == this.active) {
                newdiv.classList.add("E4M_on");
            } else {
                newdiv.classList.add("E4M_off");
            };
            newdiv.callback_arg = i;
            newdiv.addEventListener('click', this.callback, true);
            nesting_div.appendChild(newdiv);
        }
        if (this.plus_url) {
            let newdiv = document.createElement('div');  
            newdiv.innerHTML = "<p>+</p>";
            newdiv.classList.add("E4M_off");
            newdiv.onclick = () => location.href = this.plus_url;
            nesting_div.appendChild(newdiv);
            
        };
    }   
}
class smartTable{
    /**
     * @param {Element} nestingTable 
     * @param {array} regArray 
     * @param {object} settings 
     * 
     *
     * Not fully portable class ! Pretty universal if Active mode is false
     * fills in a table having id = nestingTable
     * table content is taken from regArray, 
     * settings obj allow to configure the table : 
     *  .headArray defines the header of each column
     *  .active : boolean to add action column (strictly dedicated to E4M)
     *  .IOfieldName : 0/1 value purely dedicated to E4M 
     *  .activeHeader : header for last column if active mode
     *  .colData : array containing the property for each column
     *  .colSorted : index of col sorted (-1 if none)
     *  .setCSS : if true, then add specific style for rows not confirmed or waiting
     * Each row contains colData[0], colData[1], ...
     * Clicking the header of a column sorts the table alpha ASC or num DESC
     * If regArray[n] has .rowLink set, clicking the row redirects to the link
     * 
     * required from external : css  
     */
   
    constructor(nestingTable, regArray, settings){
        this.nestingTable = nestingTable;
        this.regArray = regArray;
        this.settings = settings;
        this.Build();
    }

    Update(new_Array){
        /**
         * table is deleted, and rebuilt with new Array
         */
        this.regArray = new_Array;
        let table = document.getElementById(this.nestingTable);
        table.deleteTHead();
        table.removeChild(table.getElementsByTagName("tbody")[0]);
        this.Build();
    }
    
    Build(){
        var table = document.getElementById(this.nestingTable);
        let tableData = this.regArray;
        let Columns = this.settings.colData;
        let nbCol = Columns.length;
        let nbLig = this.regArray.length;
        var isActive = this.settings.active;
        var activeHeader = this.settings.activeHeader;
        var IOfieldName = this.settings.IOfieldName;
        let colSorted = this.settings.colSorted;
        let header = this.settings.headArray;
        let nbHead = header.length;
        if ( nbCol != nbHead ) {
            throw 'header and body have different number of columns ! '
        } 
        let columns = this.settings.colData;
        var tableHeader = table.createTHead();
        let rowHead = document.createElement('tr');
        for (let k = 0; k< nbHead; k++){
            let headerCell = document.createElement('th');
            headerCell.v_sortKey = Columns[k]; // puts the sort key in a variable attached to the cell
            let sortSign = (k == colSorted) ? str["sort_mark"] :""; 
            //let textNode = document.createTextNode(header[k]);
            //if (k == colSorted) textNode += "!";
            headerCell.appendChild(document.createTextNode(header[k] + sortSign));
            //headerCell.appendChild(textNode);
            headerCell.callback_arg = Columns[k];
            headerCell.index = k;
            headerCell.addEventListener('click', event => {
                let key = event.currentTarget.v_sortKey;
                let sortIndex = event.currentTarget.index;
                this.settings.colSorted = sortIndex;
                let tableData = this.regArray;
                switch (typeof(this.regArray[0][key])) { // first data must be representative !
                    case 'number' : 
                        tableData.sort((a,b) => -parseFloat(a[key]) + parseFloat(b[key]));	
                        break;
                    case 'string' : 
                        tableData.sort( (a,b) => a[key].toString().localeCompare(b[key].toString()) );
                        break;	
                    default :
                        throw ('sort_method handles only numbers and strings');
                }
                this.regArray = tableData;
                let table = document.getElementById(this.nestingTable);
                table.deleteTHead();
                table.removeChild(table.getElementsByTagName("tbody")[0]);
                this.Build();
            });
            headerCell.classList.add("E4M_hoverable_item");
            rowHead.appendChild(headerCell);
        }
        if (isActive) {
            rowHead.appendChild(document.createTextNode(activeHeader))
        };
        tableHeader.appendChild(rowHead);
        let tableBody = document.createElement('tbody');
        
        /* from here, code is specific to RegisterList table if isActive */

        tableData.forEach(function(rowData){
            /* let's put all columns */
            let isWaiting = (rowData.wait == "1") ? true : false;
            let isConfirmed = (rowData.confirmed == "1") ? true : false;
            let row = document.createElement('tr');
            for (let i = 0; i< nbCol; i++) {
                let cell = document.createElement('td');
                cell.appendChild(document.createTextNode(rowData[Columns[i]]));
                row.appendChild(cell);
            }
            /* Add last colunm action (makes sense only for E4M) */
            if (isActive) {
                let lastCol = document.createElement('td');
                var lastColChar = "";
                if ( isConfirmed ) {
                    lastColChar = "❌";
                    lastCol.act_arg = "d";
                } else {
                    if ( isWaiting ) {
                        lastColChar = "✅";
                        lastCol.act_arg = "c";
                    }
                }
                lastCol.char_arg = rowData.fullname;
                lastCol.numb_arg = rowData.id;
                lastCol.appendChild(document.createTextNode(lastColChar));
                lastCol.addEventListener("click", (event) => {
                    // EditRegistration (reg, action, member_name)
                    let reg_id = event.currentTarget.number_arg;
                    let action = event.currentTarget.act_arg;
                    let member_name = event.currentTarget.char_arg;
                    console.log("reg_id = ",reg_id);
                    console.log("action = ",action);
                    console.log("member_name = ",member_name);

                    EditRegistration (reg_id, action, member_name);
                })
                row.appendChild(lastCol)
            };
            if ( rowData.rowLink ) {
                row.addEventListener("click", () => {
                    document.location = rowData.rowLink;
                })
            }
            if ( rowData.css ) {
                row.classList.add(rowData.css); 
            }
            tableBody.appendChild(row);
        });
        table.appendChild(tableBody);
    }   
}   
    

