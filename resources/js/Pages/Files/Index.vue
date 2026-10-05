<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import AlertModal from '@/Components/AlertModal.vue'

const files = ref([])
const departments = ref([])
const folders = ref([])

const title = ref('')
const departmentId = ref(null)
const folderId = ref(null)
const selectedFile = ref(null)
const editingFile = ref(null)


const editTitle = ref('')
const editDepartmentId = ref(null)
const editFolderId = ref(null)
const editSelectedFile = ref(null)

const pagination = ref(null)

const alertModal = ref({
    show: false,
    type: 'success',
    message: '',
    confirm: false,
})

const alertConfirmAction = ref(null)

const showAlert = (message, type = 'success') => {
    alertModal.value = {
        show: true,
        type,
        message,
        confirm: false,
    }

    alertConfirmAction.value = null
}

const showConfirm = (message, onConfirm) => {
    alertModal.value = {
        show: true,
        type: 'warning',
        message,
        confirm: true,
    }

    alertConfirmAction.value = onConfirm
}

const closeAlert = () => {
    alertModal.value.show = false
    alertConfirmAction.value = null
}

const handleAlertConfirm = async () => {
    const action = alertConfirmAction.value

    closeAlert()

    if (action) {
        await action()
    }
}

const fetchFiles = async (url = '/files/data') => {
    const response = await axios.get(url)

    files.value = response.data.data
    pagination.value = response.data
}

const fetchDepartments = async () => {
    const response = await axios.get('/departments/data')
    departments.value = response.data
}

const fetchFolders = async () => {
    const response = await axios.get('/folders/data')
    folders.value = response.data
}
const getAllFolders = (folders, level = 0) => {
    let result = []

    folders.forEach((folder) => {
        result.push({
            ...folder,
            level: level,
        })

        if (folder.children_recursive) {
            result = result.concat(
                getAllFolders(folder.children_recursive, level + 1)
            )
        }
    })

    return result
}

const handleFileChange = (event) => {
    selectedFile.value = event.target.files[0]
}

const uploadFile = async () => {
    if (!title.value || !departmentId.value || !selectedFile.value) {
        showAlert('Title, Department, dan File wajib diisi.', 'warning')
        return
    }

    const formData = new FormData()

    formData.append('title', title.value)
    formData.append('department_id', departmentId.value)
    formData.append('folder_id', folderId.value ?? '')
    formData.append('file', selectedFile.value)

    try {
    await axios.post('/files', formData)

    title.value = ''
    departmentId.value = null
    folderId.value = null
    selectedFile.value = null

    document.getElementById('file-input').value = ''

    await fetchFiles()

    showAlert('File berhasil diupload.', 'success')
} catch (error) {
    if (error.response?.status === 422) {
        const errors = error.response.data.errors

        const firstError = Object.values(errors)?.[0]?.[0]

        showAlert(firstError || 'Data upload tidak valid.', 'error')
        return
    }

    showAlert('Terjadi kesalahan saat mengupload file.', 'error')
}
}
const updateFile = async () => {
    if (!editingFile.value) {
        return
    }

    if (!editTitle.value || !editDepartmentId.value) {
        showAlert(
            'Title dan Department wajib diisi.',
            'warning'
        )
        return
    }

    const formData = new FormData()

    formData.append('title', editTitle.value)
    formData.append('department_id', editDepartmentId.value)
    formData.append('folder_id', editFolderId.value ?? '')
    formData.append('_method', 'PUT')

    if (editSelectedFile.value) {
        formData.append('file', editSelectedFile.value)
    }

    try {
        await axios.post(`/files/${editingFile.value.id}`, formData)

        editingFile.value = null
        editTitle.value = ''
        editDepartmentId.value = null
        editFolderId.value = null
        editSelectedFile.value = null

        const editFileInput = document.getElementById('edit-file-input')

        if (editFileInput) {
            editFileInput.value = ''
        }

        await fetchFiles()

        showAlert('File berhasil diperbarui.', 'success')
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors
            const firstError = Object.values(errors)?.[0]?.[0]

            showAlert(firstError || 'Data file tidak valid.', 'error')
            return
        }

        showAlert('Terjadi kesalahan saat memperbarui file.', 'error')
    }
}

const editFile = (file) => {
    editingFile.value = file

    editTitle.value = file.title
    editDepartmentId.value = file.department_id
    editFolderId.value = file.folder_id
    editSelectedFile.value = null
}

const deleteFile = async (file) => {
    showConfirm(
        `Apakah kamu yakin ingin menghapus file "${file.file_name}"?`,
        async () => {
            try {
                await axios.delete(`/files/${file.id}`)

                await fetchFiles()

                showAlert('File berhasil dihapus.', 'success')
            } catch (error) {
                showAlert(
                    'Terjadi kesalahan saat menghapus file.',
                    'error'
                )
            }
        }
    )
}

onMounted(() => {
    fetchFiles()
    fetchDepartments()
    fetchFolders()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="mb-6">
      <a
        href="/dashboard"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-900"
      >
        ← Kembali ke Dashboard
      </a>
    </div>

    <div class="mb-8 text-center">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
        File Management
    </h1>

    <p class="mt-2 text-gray-500">
        Kelola, upload, edit, dan download dokumen perusahaan.
    </p>
    </div>

    <!-- Upload + Daftar File -->
<div
    class="mt-6 grid items-start gap-6"
    style="grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);"
>

    <!-- Form Upload -->
    <div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
    style="grid-column: 1;"
>

      <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
        <div class="flex items-center gap-3">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600"
          >
            📤
          </div>

          <div>
            <h2 class="text-lg font-semibold text-gray-900">Upload File</h2>

            <p class="text-sm text-gray-500">
              Tambahkan dokumen baru ke dalam sistem.
            </p>
          </div>
        </div>
      </div>

      <div class="space-y-5 p-6">
        <div>
          <label class="block text-sm font-medium text-gray-700"> Title </label>

          <input
            v-model="title"
            type="text"
            class="mt-2 w-full rounded-lg border-gray-300 px-3 py-2.5 shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
            placeholder="Contoh: Laporan Keuangan 2026"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">
            Department
          </label>

          <select
            v-model="departmentId"
            class="mt-2 w-full rounded-lg border-gray-300 px-3 py-2.5 shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
          >
            <option :value="null">Pilih Department</option>

            <option
              v-for="department in departments"
              :key="department.id"
              :value="department.id"
            >
              {{ department.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">
            Folder
          </label>

          <select
            v-model="folderId"
            class="mt-2 w-full rounded-lg border-gray-300 px-3 py-2.5 shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
          >
            <option :value="null">Root Folder</option>

            <option
              v-for="folder in getAllFolders(folders)"
              :key="folder.id"
              :value="folder.id"
            >
              {{ folder.id === folderId
                        ? folder.name
                        : `${'  '.repeat(folder.level)}${folder.level > 0 ? '└─ ' : '📁 '}${folder.name}`
              }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700"> File </label>

          <input
            id="file-input"
            type="file"
            class="mt-2 block w-full cursor-pointer rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700"
            @change="handleFileChange"
          />
        </div>

                <div class="flex justify-end pt-2">
          <button
            type="button"
            @click="uploadFile"
            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            📤 Upload File
          </button>
        </div>
      </div>
    </div>

    <!-- Daftar File -->
<div class="min-w-0">


    <div class="mt-0 max-h-[500px] overflow-y-auto pr-2">
      <div class="mb-4 flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold text-gray-900">Daftar File</h2>
          <p class="mt-1 text-sm text-gray-500">
            Dokumen yang tersimpan di dalam sistem.
          </p>
        </div>

        <div
          class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700"
        >
          {{ files.length }} File
        </div>
      </div>

      <div
        v-for="file in files"
        :key="file.id"
        class="mb-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md"
      >
        <div class="p-4">
          <div
            class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
          >
            <!-- Informasi File -->
            <div class="min-w-0 flex-1">
              <div class="flex items-start gap-4">
                <div
                  class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-2xl"
                >
                  📄
                </div>

                <div class="min-w-0">
                  <h3 class="truncate text-lg font-semibold text-gray-900">
                    {{ file.title }}
                  </h3>

                  <p class="mt-1 truncate text-sm text-gray-500">
                    {{ file.file_name }}
                  </p>

                  <div class="mt-2 flex flex-wrap items-center gap-2">
  <span
    class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700"
  >
    {{ file.department?.name }}
  </span>

  <span
    class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
  >
    📁 {{ file.folder?.name ?? 'Root Folder' }}
  </span>

  <span class="text-xs text-gray-400">•</span>

  <span class="text-xs text-gray-500">
    Uploaded By:
    <span class="font-medium text-gray-700">
      {{ file.uploader?.name }}
    </span>
  </span>
</div>
                </div>
              </div>
            </div>

            <!-- Action -->
            <div class="flex shrink-0 flex-wrap gap-2">
              <a
                :href="`/files/${file.id}/download`"
                class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700"
              >
                ↓ Download
              </a>

              <button
                type="button"
                @click="editFile(file)"
                class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-yellow-600"
              >
                ✎ Edit
              </button>

              <button
                type="button"
                @click="deleteFile(file)"
                class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
              >
                🗑 Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Form Edit -->

        <!-- Form Edit -->
        <div
          v-if="editingFile?.id === file.id"
          class="mt-4 rounded-lg border bg-gray-50 p-4"
        >
          <h3 class="font-semibold">Edit File</h3>

          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">
              Title
            </label>

            <input
              v-model="editTitle"
              type="text"
              class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
            />
          </div>

          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">
              Department
            </label>

            <select
              v-model="editDepartmentId"
              class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
            >
              <option :value="null">Pilih Department</option>

              <option
                v-for="department in departments"
                :key="department.id"
                :value="department.id"
              >
                {{ department.name }}
              </option>
            </select>
          </div>

          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">
              Folder
            </label>

            <select
              v-model="editFolderId"
              class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
            >
              <option :value="null">Root Folder</option>

              <option
                v-for="folder in folders"
                :key="folder.id"
                :value="folder.id"
              >
                {{ folder.name }}
              </option>
            </select>
          </div>

          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">
              Ganti File
            </label>

            <input
              id="edit-file-input"
              type="file"
              class="mt-1 w-full"
              @change="editSelectedFile = $event.target.files[0]"
            />

            <p class="mt-1 text-xs text-gray-500">
              Kosongkan jika tidak ingin mengganti file.
            </p>
          </div>

          <div class="mt-4 flex gap-2">
            <button
              type="button"
              @click="updateFile"
              class="rounded bg-blue-600 px-4 py-2 text-white"
            >
              Update File
            </button>

            <button
              type="button"
              @click="editingFile = null"
              class="rounded bg-gray-500 px-4 py-2 text-white"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>

      <p v-if="files.length === 0" class="mt-2 text-gray-500">
        Belum ada file.
      </p>
    </div>
    <div
      v-if="pagination && pagination.last_page > 1"
      class="mt-6 flex items-center justify-between"
    >
      <button
        type="button"
        @click="fetchFiles(pagination.prev_page_url)"
        :disabled="!pagination.prev_page_url"
        class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
      >
        Previous
      </button>

      <span class="text-sm text-gray-600">
        Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
      </span>

      <button
        type="button"
        @click="fetchFiles(pagination.next_page_url)"
        :disabled="!pagination.next_page_url"
        class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
      >
        Next
      </button>
    </div>
  </div>
</div>
  <AlertModal
    :show="alertModal.show"
    :type="alertModal.type"
    :message="alertModal.message"
    :confirm="alertModal.confirm"
    @close="closeAlert"
    @confirm="handleAlertConfirm"
  />
  </div>
</template>
