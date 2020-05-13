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
      endDay: '',
      sourceOp: 'AND',
      sourceNotes: '',
      serialOp: '',
      serialList: '',
      eventTypeSelected: [],
      eventTypeList: [],
      causeTypeSelected: [],
      causeTypeList: []
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

class QueryDesign {
  getInstance() {
    return getQueryDesignInstance()
  }

  fillEventList(eventList) {
    const mapToVueSelect = function(eventId) {
      return {
        value: eventId,
        text: eventList[eventId]['EventName'],
        helpText: eventList[eventId]['EventDesc']
      }
    }
    const predefinedEvents = Object.keys(eventList).filter(function(eventId) {
      return parseInt(eventList[eventId]['EventPredefined']) > 0
    })
    const localEvents = Object.keys(eventList).filter(function(eventId) {
      return parseInt(eventList[eventId]['EventPredefined']) < 1
    })
    const vue = this.getInstance()
    vue._data.eventTypeSelected = []
    vue._data.eventTypeList = []
      .concat(predefinedEvents)
      .concat(localEvents)
      .map(mapToVueSelect)
    return this
  }

  fillCauseList(causeList) {
    const mapToVueSelect = function(causeId) {
      return {
        value: causeId,
        text: causeList[causeId]['CauseName'],
        helpText: causeList[causeId]['CauseDesc']
      }
    }
    const predefinedCauses = Object.keys(causeList).filter(function(causeId) {
      return parseInt(causeList[causeId]['CausePredefined']) > 0
    })
    const localCauses = Object.keys(causeList).filter(function(causeId) {
      return parseInt(causeList[causeId]['CausePredefined']) < 1
    })
    const vue = this.getInstance()
    vue._data.causeTypeSelected = []
    vue._data.causeTypeList = []
      .concat(predefinedCauses)
      .concat(localCauses)
      .map(mapToVueSelect)
    return this
  }

  initForm(params) {
    const vue = getQueryDesignInstance()
    vue._data.beginYear = params.MinYear
    vue._data.endYear = params.MaxYear
    return this
  }

  setForm(data) {
    const vue = getQueryDesignInstance()
    vue._data.beginYear = data.D_DisasterBeginTime[0]
    vue._data.beginMonth = data.D_DisasterBeginTime[1]
    vue._data.beginDay = data.D_DisasterBeginTime[2]
    vue._data.endYear = data.D_DisasterEndTime[0]
    vue._data.endMonth = data.D_DisasterEndTime[1]
    vue._data.endDay = data.D_DisasterEndTime[2]
    vue._data.sourceOp = data.D_DisasterSource[0]
    vue._data.sourceNotes = data.D_DisasterSource[1]
    vue._data.serialOp = data.D_DisasterSerial[0]
    vue._data.serialList = data.D_DisasterSerial[1]
    vue._data.eventTypeSelected = data.D_EventId
    vue._data.causeTypeSelected = data.D_CauseId
  }
}

export { QueryDesign }
export default { getQueryDesignInstance, readFile, loadQueryFromFile }
