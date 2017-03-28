/* exported formatColumn */

class WatchingCheckbox extends React.Component {
    constructor (prop) {
        super();
        this.state = {
            checked:prop.checked,
            issue_id:prop.issue_id
        };
        this.onChange = this.onChange.bind(this);
    }
    render () {
        return (
            <td>
                <input type="checkbox" checked={this.state.checked} onChange={this.onChange}/>
            </td>
        );
    }
    onChange (e) {
        console.log(e._targetInst);
        console.log(this);
        var that = this;
        $.ajax({
            type:"PUT",
            dataType: "json",
            url:"/issue_tracker/ajax/ToggleMyWatching.php?issue_id=" + this.state.issue_id,
            success: function (data) {
                console.log(data);
                
                that.setState({
                    checked:data.watching
                });
            },
            error: function (err) {
                console.log("Error", err);
            }
        });
    }
}

/**
 * Modify behaviour of specified column cells in the Data Table component
 * @param {string} column - column name
 * @param {string} cell - cell content
 * @param {arrray} rowData - array of cell contents for a specific row
 * @param {arrray} rowHeaders - array of table headers (column names)
 * @return {*} a formated table cell for a given column
 */
function formatColumn(column, cell, rowData, rowHeaders) {
  // If a column if set as hidden, don't display it
  if (loris.hiddenHeaders.indexOf(column) > -1) {
    return null;
  }

  // Create the mapping between rowHeaders and rowData in a row object.
  var row = {};
  rowHeaders.forEach(
    function(header, index) {
      row[header] = rowData[index];
    },
    this
  );

  if (column === 'Title') {
    let cellLinks = [];
    cellLinks.push(
      <a href={loris.BaseURL + "/issue_tracker/edit/?issueID=" +
      row['Issue ID'] + "&backURL=/issue_tracker/"}>
        {row.Title}
      </a>
    );
    return (
      <td>
        {cellLinks}
      </td>
    );
  }

  if (column === 'Issue ID') {
    let cellLinks = [];
    cellLinks.push(
      <a href={loris.BaseURL + "/issue_tracker/edit/?issueID=" +
      row['Issue ID'] + "&backURL=/issue_tracker/"}>
        {cell}
      </a>
    );
    return (<td>{cellLinks}</td>);
  }

  if (column === 'Priority') {
    switch (cell) {
      case "normal":
        return <td style={{background: "#CCFFCC"}}>Normal</td>;
      case "high":
        return <td style={{background: "#EEEEAA"}}>High</td>;
      case "urgent":
        return <td style={{background: "#CC6600"}}>Urgent</td>;
      case "immediate":
        return <td style={{background: "#E4A09E"}}>Immediate</td>;
      case "low":
        return <td style={{background: "#99CCFF"}}>Low</td>;
      default:
        return <td>None</td>;
    }
  }

  if (column === 'PSCID' && row.PSCID !== null) {
    let cellLinks = [];
    cellLinks.push(
      <a href={loris.BaseURL + "/" +
      row.CandID + "/"}>
        {cell}
      </a>
    );
    return (
      <td>
        {cellLinks}
      </td>
    );
  }

  if (column === 'Visit Label' && row['Visit Label'] !== null) {
    let cellLinks = [];
    cellLinks.push(
      <a href={loris.BaseURL + "/instrument_list/?candID=" +
              row.CandID + "&sessionID=" + row.SessionID }>
        {cell}
      </a>
    );
    return (
      <td>
        {cellLinks}
      </td>
    );
  }
  if (column === 'Watching') {
    return (<WatchingCheckbox checked={cell==="1"} issue_id={row['Issue ID']}/>);
  }

  return <td>{cell}</td>;
}

window.formatColumn = formatColumn;

export default formatColumn;
