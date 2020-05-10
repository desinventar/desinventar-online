import Vue from 'vue'

// @ts-ignore
window.queryDesignInit = function() {
  new Vue({
    el: '#frmMainQuery',
    data: {
      beginYear: '',
      beginMonth: '',
      beginDay: '',
      endYear: '',
      endMonth: '',
      endDay: ''
    }
  })
}

function getQueryDesignInstance() {
  // @ts-ignore
  return document.getElementById('frmMainQuery').__vue__ || false
}

async function readFile(file) {
  return new Promise((resolve, reject) => {
    let reader = new FileReader()
    reader.onload = function() {
      return resolve(reader.result.toString())
    }
    reader.onerror = reject
    reader.readAsText(file)
  })
}

async function extractQueryFromXml(xml) {
  const response = await jQuery.post(jQuery('#desinventarURL').val() + '/', {
    cmd: 'cmdQueryOpen',
    xmlString: xml
  })
  return JSON.parse(response)
}

async function loadQueryFromFile(file) {
  const xml = await this.readFile(file)
  const response = await extractQueryFromXml(xml)
  return response
}

export default {
  getQueryDesignInstance,
  readFile,
  loadQueryFromFile
}
