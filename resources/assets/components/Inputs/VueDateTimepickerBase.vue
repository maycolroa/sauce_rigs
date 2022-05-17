
<script>
import datetime from 'vuejs-datetimepicker';

const daysES = ['DOM', 'LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SA'];

export default {
  extends: datetime,
  data () {
    return {
      months: ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
    }
  },
  created() {
    this.days.splice(0);

    daysES.forEach((day, idx) => {
      this.days[(idx - this.normalizedFirstDayOfWeek + 7) % 7] = day;
    });
  },
  mounted() {
    this.$el.querySelector(".okButton").textContent = "Limpiar";
    this.$el.querySelector(".okButton.ok").textContent = "Ok";
  },
}
</script>

<style scoped>

  .year-month-wrapper{
    background-color: white;
  }

  input{
    min-width: 270px;
    width:100%;
    height: 30px;
    padding: 3px;
    border: 1px solid #D3D7DC;
  }
  .datetime-picker{
    position: relative;
  }
  .calender-div{
    min-width: 300px;
    box-shadow: 1px 2px 5px;
    background: #FFF;
    position: absolute;
    display: inline-block;
    left: 0;
    top: 35px;
    color: #444;
    font-size: 13px;
    padding-bottom: 10px;
    z-index: 100;
  }
  .port, .days{
    display: inline-block;
    width: 35px;
    height: 30px;
    padding: 5px 3px;
    margin: 2px;
    border-radius: 2px;
    text-align: center;
    vertical-align: top;
    cursor: pointer;
  }
  .days{
    color: black;
    /*font-weight: bold;*/
  }
  .port:hover{
    color: blue;
    /*font-weight: bold;*/
  }
  .activePort, .activePort:hover {
    background-color: #ECE9E9;
    color: black;
  }
  .month-setter, .year-setter{
    margin: 0 1px;
    width: 48.2%;
    color: white;
    font-weight: 900;
    display: inline-block;
  }
  .nav-l:hover, .nav-r:hover {
    background-color: white;
  }
  .nav-l, .nav-r {
    display: inline-block;
    width: 25px;
    background-color: white;
    color: black;
    font-size: 16px;
    cursor: pointer;
    border: 0;
    padding: 7px;
    margin:0;
  }
  .nav-l:focus, .nav-r:focus{
    outline: none;
  }
  .nav-l{
    float: left;
  }
  .nav-r{
    float: right;
  }
  .month, .year{
    width: 60px;
    text-align: right;
    display: inline-block;
    color: black;
    padding: 7px 0;
  }
  /* .headers>span{

  } */
  .hour-selector, .minute-selector{
    width: 40px;
    display: inline-block;
    text-align: center;
    font-weight: bold;
    position: relative;
    cursor: pointer;
  }
  .time-separator{
    display: inline-block;
    font-weight: bold;
  }
  .time-picker{
    margin: 10px
  }
  .nav-t, .nav-d{
    font-weight: bold;
    cursor: pointer;
  }
  .scroll-hider {
    display: none;
    vertical-align:top;
    overflow:hidden;
    border:0;
    position: absolute;
    top: -40px;
    left: 0;
    box-shadow: 0 0 3px #fff;
    background-color: white;
  }
  .scroll-hider ul {
    padding:5px;
    margin:-5px -13px -5px -5px;
    list-style-type: none;
    height: 100px;
    overflow: auto;
    width:55px;
    color: #999;
    overflow-x: hidden;
  }
  .showSelector{
    display:inline-block;
  }
  li.active{
    background-color: white;
    color: 999;
  }
  li{
    padding: 4px;
    font-size: 16px;
    width: 100%;
    cursor: pointer;
  }
  .time-picker{
    display: inline-block;
  }
  .noDisplay{
    display: none;
  }
  .okButton{
    color: black;
    font-size: 15px;
    font-weight: bold;
    padding: 0;
    float: right;
    border: 0;
    margin-right: 10px;
    margin-top: 10px;
    cursor: pointer;
    background: transparent;
  }

  #tj-datetime-input {
    outline-color: #f0635f !important;
  }
</style>