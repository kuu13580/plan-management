function escapeStr(str) {
  str = str.replace(/\n/g, "<br>");
  return str;
}
const data_type = {
  "command": "text",
  "key": "text",
  "event_key": "text",
  "stime": "time",
  "duration": "time",
  "contents": "text",
  "cost": "number",
  "type": "type",
  "stage": "text",
  "others": "none",
  "last": "number",
  "block_id": "number",
  "page": "text"
}
function check_input(data) {
  let check = true;
  for (let key in data) {
    let str = data[key];
    let type = data_type[key];
    switch (type) {
      case "text":
        check = check && !/[\t\r\n]|^ *$/.test(str);
        break;
      case "time":
        check = check && /^\d{2}(:\d{2}){1,2}$/.test(str);
        break;
      case "number":
        check = check && !/[^\d]/.test(str);
        break;
      case "type":
        check = check && /^(transportation|schedule)$/.test(str);
        break;
      case "none":
        check = check && true;
        break;
      default:
        check = false;
        break;
    }
  }
  return check;
}
