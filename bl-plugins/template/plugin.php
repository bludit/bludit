<?php

// Settings of the Template theme, the editor of its HTML and CSS files.
// The files are compiled by HtmlTemplate, the same as the API does.
class template extends Plugin
{

	public function init()
	{
		$this->dbFields = array();
	}

	public function post()
	{
		global $L;

		if (isset($_POST['revert'])) {
			$result = HtmlTemplate::revert();
			if ($result !== true) {
				Alert::set($result, ALERT_STATUS_FAIL);
				return false;
			}
			Alert::set($L->get('The template has been reverted'));
			return false;
		}

		$files = array();
		$posted = isset($_POST['files']) && is_array($_POST['files']) ? $_POST['files'] : array();
		foreach (HtmlTemplate::FILES as $name => $description) {
			if (isset($posted[$name]) && is_string($posted[$name])) {
				// The browser sends the textarea with CRLF line breaks
				$files[$name] = str_replace("\r\n", "\n", $posted[$name]);
			}
		}

		$result = HtmlTemplate::save($files);
		if ($result !== true) {
			// Keep what the user wrote, the form shows it again with the error
			Session::set('templateDraft', $files);
			Alert::set($result, ALERT_STATUS_FAIL);
			return false;
		}

		return true;
	}

	public function form()
	{
		global $L;

		$files = HtmlTemplate::files();
		$draft = Session::get('templateDraft');
		if (is_array($draft)) {
			$files = array_merge($files, $draft);
			Session::remove('templateDraft');
		}

		$html = '<p class="text-muted">' . htmlspecialchars($L->get('template-help'), ENT_QUOTES, 'UTF-8') . '</p>';

		// One tab per file
		$html .= '<ul class="nav nav-tabs" role="tablist">';
		$first = true;
		foreach (HtmlTemplate::FILES as $name => $description) {
			$id = 'template-' . preg_replace('/[^a-z0-9]/', '-', $name);
			$html .= '<li class="nav-item" role="presentation">';
			$html .= '<a class="nav-link' . ($first ? ' active' : '') . '" data-bs-toggle="tab" href="#' . $id . '" role="tab">' . $name . '</a>';
			$html .= '</li>';
			$first = false;
		}
		$html .= '</ul>';

		$html .= '<div class="tab-content pt-3">';
		$first = true;
		foreach (HtmlTemplate::FILES as $name => $description) {
			$id = 'template-' . preg_replace('/[^a-z0-9]/', '-', $name);
			$html .= '<div class="tab-pane' . ($first ? ' show active' : '') . '" id="' . $id . '" role="tabpanel">';
			$html .= '<div class="form-text mb-2">' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '</div>';
			$html .= '<textarea class="form-control template-editor" name="files[' . $name . ']" rows="24" spellcheck="false" autocomplete="off" aria-label="' . $name . '">';
			$html .= htmlspecialchars($files[$name], ENT_QUOTES, 'UTF-8');
			$html .= '</textarea>';
			$html .= '</div>';
			$first = false;
		}
		$html .= '</div>';

		$html .= '<button type="submit" name="revert" value="1" class="btn btn-secondary btn-sm mt-3">' . $L->get('Revert to the previous version') . '</button>';

		// Placeholders the files can use
		$html .= '<details class="mt-4"><summary class="fw-semibold">' . $L->get('Placeholders') . '</summary>';
		$html .= '<div class="table-responsive mt-2"><table class="table table-sm"><thead><tr>';
		$html .= '<th>' . $L->get('Placeholder') . '</th><th>' . $L->get('Use') . '</th><th>' . $L->get('Description') . '</th>';
		$html .= '</tr></thead><tbody>';
		foreach (HtmlTemplate::reference() as $name => $placeholder) {
			$use = array();
			if ($placeholder['print']) {
				$use[] = '<code>{{ ' . $name . ' }}</code>';
			}
			if ($placeholder['if']) {
				$use[] = '<code>{{#if ' . $name . '}}</code>';
			}
			$description = htmlspecialchars($placeholder['description'], ENT_QUOTES, 'UTF-8');
			if (isset($placeholder['only'])) {
				$description .= ' <span class="text-muted">(' . $L->get('Only in') . ' ' . implode(', ', $placeholder['only']) . ')</span>';
			}
			$html .= '<tr><td><code>' . $name . '</code></td><td>' . implode(' ', $use) . '</td><td>' . $description . '</td></tr>';
		}
		$html .= '</tbody></table></div></details>';

		$html .= '<style>.template-editor{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:.85rem;line-height:1.5;tab-size:4;white-space:pre;}</style>';

		return $html;
	}
}
