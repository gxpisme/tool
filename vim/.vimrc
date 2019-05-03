" 修改leader键
let g:mapleader = ','
let mapleader = ','

" 开启语法高亮
syntax on

" 在上下移动光标时，光标的上方或下方至少会保留显示的行数
set scrolloff=7

" 设置文件编码
set encoding=utf-8 fileencodings=ucs-bom,utf-8,cp936

" 设置一个tab为4个空格
set ts=4

" 表示一个 tab 显示出来是多少个空格的长度，默认 8
set tabstop=4
set softtabstop=4
set shiftwidth=4

" tab转换为空格
set expandtab

" 自动将tab转为4个空格 ret replace tab
:%retab!

" 设置自动缩进
set autoindent

" 设置php的自动补全
autocmd FileType php set omnifunc=phpcomplete#CompletePHP

" 设置行号
set number

" 设置目录树
map <F2> :NERDTreeToggle<CR>

" Taglist
map <F3> :TlistToggle<CR>

nmap <F8> :TagbarToggle<CR>

" 粘贴toggle
set pastetoggle=<F4>

" 括号配对情况, 跳转并高亮一下匹配的括号
set showmatch

" 在状态栏显示正在输入的命令
set showcmd

" 左下角显示当前vim模式
set showmode

" 设置文内智能搜索提示
" 高亮search命中的文本
set hlsearch
hi Search ctermbg=LightYellow
hi Search ctermfg=Red

" 打开增量搜索模式,随着键入即时搜索
set incsearch
" 搜索时忽略大小写
set ignorecase
" 有一个或以上大写字母时仍大小写敏感
set smartcase
" 设置vim高亮颜色
highlight Search term=reverse ctermbg=11 guibg=Blue

" 设置字典
au FileType php call AddPHPFuncList()
function AddPHPFuncList()
    set dictionary-=~/.vim/funclist.txt dictionary+=~/.vim/funclist.txt
    set complete-=k complete+=k
    " 设置tab为空格 replace tab
    :%retab!
    :FixWhitespace
endfunction


" 插件配置
call plug#begin()
Plug 'kien/ctrlp.vim'
Plug 'scrooloose/nerdtree'
Plug 'altercation/solarized'
Plug 'vim-scripts/taglist.vim'
Plug 'bronson/vim-trailing-whitespace'
Plug 'Valloric/YouCompleteMe'
Plug 'majutsushi/tagbar'
Plug 'terryma/vim-multiple-cursors'
Plug 'scrooloose/syntastic'
call plug#end()

" ctrp 插件
let g:ctrlp_cmd = 'CtrlP'
let g:ctrlp_custom_ignore = {
    \ 'dir':  '\v[\/]\.(git|hg|svn|rvm)$',
    \ 'file': '\v\.(exe|so|dll|zip|tar|tar.gz|pyc)$',
    \ }
let g:ctrlp_working_path_mode=0
let g:ctrlp_match_window_bottom=1
let g:ctrlp_max_height=15
let g:ctrlp_match_window_reversed=0
let g:ctrlp_mruf_max=500
let g:ctrlp_follow_symlinks=1

" YouCompleteMe插件
let g:ycm_global_ycm_extra_conf='~/.ycm_extra_conf.py'  "设置全局配置文件的路径
let g:ycm_seed_identifiers_with_syntax=1    " 语法关键字补全
let g:ycm_confirm_extra_conf=0  " 打开vim时不再询问是否加载ycm_extra_conf.py配置
set completeopt=longest,menu    "让Vim的补全菜单行为与一般IDE一致(参考VimTip1228)

" 快捷键
map <leader>w :w<cr>
map <leader>q :q<cr>
map <leader>x :x<cr>
map <leader>n :set nu!<cr>
map <leader><space> :FixWhitespace<cr>
iab xdt <c-r>=strftime("%Y-%m-%d %H:%M:%S")<cr>

" powerline
set rtp+=/usr/lib/python2.7/site-packages/powerline/bindings/bash/powerline.sh
set nocompatible
set t_Co=256
let g:minBufExplForceSyntaxEnable = 1
python from powerline.vim import setup as powerline_setup
python powerline_setup()
python del powerline_setup
set laststatus=2
set guifont=Source\ Code\ Pro\ for\ Powerline:h12
set noshowmode

" 块选择
let g:multi_cursor_use_default_mapping=0
" Default mapping
let g:multi_cursor_next_key='<C-m>'
let g:multi_cursor_prev_key='<C-p>'
let g:multi_cursor_skip_key='<C-x>'
let g:multi_cursor_quit_key='<Esc>'

" Vim恢复文件关闭之前光标的位置
if has("autocmd")
    " In text files, always limit the width of text to 78 characters
    autocmd BufRead *.txt set tw=78
    " When editing a file, always jump to the last cursor position
    autocmd BufReadPost *
    \ if line("'\"") > 0 && line ("'\"") <= line("$") |
    \ exe "normal g'\"" |
    \ endif
endif

" 语法检测
let g:syntastic_always_populate_loc_list = 1
let g:syntastic_auto_loc_list = 1
let g:syntastic_check_on_open = 1
let g:syntastic_check_on_wq = 0
let g:syntastic_php_checkers = ['php', 'phpcs', 'phpmd']
let g:syntastic_python_checkers = ['pylint']


" 执行文件
map <F5> <ESC>: call RunThisScript() <CR>
function RunThisScript()
    let file_name = expand("%:p")
    let file_ext = expand("%:e")
    let file_cmd = ""

    "python 直接调用
    if file_ext == "py"
        let file_cmd = '/usr/bin/python'
        let file_args = ' ' . file_name
    "c 需要提取第一行的编译参数
    "如，当需要引入第三方库(以mysql为例)时，则在第一行添加: //-lmysqlclient -L/usr/local/mysql/include
    "文件中则可直接　#include <mysql/mysql.h>
    elseif file_ext == "c"
        let file_first_line = getline(1)
        let file_arg = ""
        if strpart(file_first_line, 0, 2) == '//'
            let file_arg = strpart(file_first_line, 2) "提取参数
        endif
        let file_output_file = strpart(file_name, 0, strridx(file_name, '.c'))
        let file_args = ' -o '. file_output_file .' '.  file_name . ' '. file_arg .' && '. file_output_file "将参数附加到编译命令之后
        let file_cmd = '/usr/bin/cc'
    "php 直接调用
    elseif file_ext == "php"
        let file_cmd = "/usr/bin/php" "php执行路径
        let file_args = ' -f '. file_name
    "perl 直接调用
    elseif file_ext == "perl" || file_ext == "pl"
        let file_cmd = "/usr/bin/perl"
        let file_args = " ". file_name
    "erlang 默认调用 main 函数, 可以确保 escript 和 noshell/shell 执行时一致
    elseif file_ext == "erl"
        let file_output_file = strpart(expand("%"), 0, stridx(expand("%"), ".erl"))
        let file_cmd = "/usr/bin/erlc"
        let file_args = file_output_file .".". file_ext ." ; /usr/bin/erl -noshell -s ". file_output_file . " main  -s init stop"
    "java 先调用 javac，再调用java
    elseif file_ext == "java"
        let file_output_file = strpart(expand("%"), 0, stridx(expand("%"), ".java"))
        let file_cmd = 'javac'
        let file_args = file_name ." && java ". file_output_file
    else
        echo "错误: 没有任何编译器匹配此文件类型, 请确认您的文件扩展名!"
    endif

    if file_cmd != ""
        if ! executable(file_cmd)
            echo file_cmd
            echo "The executable file to compile ". file_ext . " type files."
        else
            let cmd = "! ". file_cmd . ' ' . file_args
            "echo "执行命令: ". cmd
            exec cmd
        endif
    endif
endfunction
