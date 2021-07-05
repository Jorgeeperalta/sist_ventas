<template>
  <v-app>
    <v-card class="mx-auto" width="1150">
      <br />
      <v-label><h1 style="margin-left: 10px">Informe</h1></v-label>
      <br />
      <v-label
        ><h3 style="margin-left: 10px">
          Filtrar por Apellido ó Fecha ó Tipo de Pago ó Ambos
        </h3></v-label
      >
      <v-row>
        <v-col md="3">
          <v-autocomplete
            style="margin-left: 10px; margin-top: 30px"
            v-model="value"
            :items="clientes"
            item-text="apellido"
            item-value="apellido"
            dense
            filled
            label="Buscar por apellido"
          ></v-autocomplete>
        </v-col>
        <v-col md="3" style="margin-top: 30px">
          <date-picker
            label="Fecha"
            v-model="time1"
            valueType="format"
          ></date-picker
        ></v-col>
        <v-col md="3" style="margin-top: 30px">
          <v-select
            label="Tipo de Pago"
            :items="tiposdepago"
            v-model="tipopago"
            outlined
            multiple
          ></v-select>
        </v-col>
        <v-col md="3" style="margin-top: 30px">
          <v-btn color="green" @click="filtar()" outlined>Buscar</v-btn>
          <v-btn
            style="margin-left: 30px"
            color="red"
            @click="limpiar()"
            outlined
            >Limpiar</v-btn
          >
        </v-col>
      </v-row>
      <v-label v-if="totalventa != 0"
        ><h3 style="margin-left: 10px">Total: {{ totalventa }}</h3></v-label
      >

      <v-data-table
        :headers="headers"
        :items="tableventas"
        :items-per-page="5"
        class="elevation-1"
      >
        <template v-slot:[`item.actions`]="{ item }">
          <v-icon small class="mr-2" @click="amplia(item)"> mdi-pencil </v-icon>
          <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
        </template>
      </v-data-table>
      <v-dialog v-model="dialog" width="500px">
        <v-card height="700px">
          <br />
          <div>
            <div style="margin-left: 350px">
              <v-label>Fecha: {{ detalle.fecha }} <br /> </v-label>
            </div>
            <br />
            <div style="margin-left: 50px">
              <v-label>Cliente: {{ detalle.apellido }} <br /> </v-label>
            </div>
            <br />
             <div style="margin-left: 50px" v-if="detalle.tipoventa=='Acopio'">
              <v-label>Estado: {{ detalle.tipoventa }} <br> Fecha Estimada de retiro {{detalle.fecharetiro}} <br /> </v-label>
            </div>
            <div v-else style="margin-left: 50px"> <v-label>Estado: {{ detalle.tipoventa }} <br> Fecha de salida: {{detalle.fechasalida}} </v-label> </div>
            <br />
            <div style="margin-left: 50px">
              <v-label>Articulos <br /> </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label v-for="art in strarts" :key="art">
                {{ art }} <br />
              </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label>Tipo de Pago: {{ detalle.tipopago }} <br /> </v-label>
            </div>
            <div style="margin-left: 50px">
              <v-label>Total: {{ detalle.monto }} <br /> </v-label>
            </div>
            <br />
             <div style="margin-left: 50px" v-if="detalle.tipoventa=='Acopio'">
              <v-btn outlined color="red"  @click="actualizaestado(detalle.id)">Retirado</v-btn>
            </div>

          </div>
        </v-card>
      </v-dialog>
    </v-card>
  </v-app>
</template>
<script>
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
export default {
  components: { DatePicker },
  data() {
    return {
      detalle: [],
      tipopago: null,
      dialog: false,
      time1: null,
      aux: [],
      clientes: [],
      value: null,
      ventas: [],
      totalventa: 0,
      tiposdepago: ["Contado", "Targeta", "Cheque", "Cta. DNI", "Mercado Pago", "Transferencia", "Debe"],
      headers: [
        {
          text: "Apellido y nombre",
          align: "start",
          sortable: false,
          value: "apellido",
        },
        { text: "Fecha", value: "fecha" },
        { text: "Monto", value: "monto" },
        { text: "Tipo de pago", value: "tipopago" },
         { text: "Estado", value: "tipoventa" },
        { text: "Acciones", value: "actions", sortable: false },
      ],
      ventas: [],
      tableventas: [],
      strarts: [],
    };
  },
  created() {
    this.cargaclientes();
    this.cargaventas();
  },
  methods: {
    actualizaestado(pk){
      alert(pk)
          var formdata = new FormData();

      var requestOptions = {
        method: "PUT",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/ventas.php?id=" +
          pk +
          "&tipoventa=" +
          'Retirado'  +
          "&fechasalida=" +
          new Date().toISOString().substr(0, 10) +
          "",
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .catch((error) => console.log("error", error));
    },
    deleteItem(item) {
   //   console.log(item);
      const index = this.tableventas.indexOf(item);

      var option= confirm("Desea eliminar esta venta! \n  OK or Cancelar.");
      if(option){
            var formdata = new FormData();

      var requestOptions = {
        method: "DELETE",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/ventas.php?id=" + item.id,
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result),this.cargaventas())
       
        .catch((error) => console.log("error", error));
      }
     

      
    },
    amplia(arts) {
      this.detalle = arts;
      var aux = this.detalle.strarticulos;
      this.strarts = aux.split(["-"]);

      this.dialog = true;
    },
    limpiar() {
      this.tableventas = [];
      this.time1 = null;
      this.value = null;
      this.totalventa = 0;
      this.aux = [];
      this.tipopago = null;
    },
    filtar() {
      if (this.value != null && this.time1 != null && this.tipopago != null) {
       // alert(this.value + " " + this.time1 + " " + this.tipopago)
        this.ventas.forEach((element) => {
          if (this.value == element.apellido && this.time1 == element.fecha && this.tipopago == element.tipopago && element.tipopago !='Debe') {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.time1 != null && this.tipopago != null) {
        this.ventas.forEach((element) => {
          if (this.time1 == element.fecha && this.tipopago == element.tipopago && element.tipopago !='Debe') {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      }
       else if (this.time1 != null ) {
        this.ventas.forEach((element) => {
          if (this.time1 == element.fecha && element.tipopago !='Debe') {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.value != null) {
        this.ventas.forEach((element) => {
          if (this.value == element.apellido && element.tipopago !='Debe') {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else if (this.tipopago != null) {
        this.ventas.forEach((element) => {
          if (this.tipopago == element.tipopago && element.tipopago !='Debe') {
            this.aux.push(element);
            this.totalventa += parseInt(element.monto);
          }
        });
      } else {
        confirm("No se encontraron resultados");
      }
      this.tableventas = this.aux;
      console.log(this.ventas);
    },
    cargaclientes() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/clientes.php",
        requestOptions
      )
        .then((response) => response.json())
        .then((result) => (this.clientes = result))
        .catch((error) => console.log("error", error));
    },

    cargaventas() {
      var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/ventas.php",
        requestOptions
      )
        .then((response) => response.json())
        //.then((result) => (console.log(result)))
        .then((result) => (this.ventas = result))
        .catch((error) => console.log("error", error));
    },
  },
};
</script>