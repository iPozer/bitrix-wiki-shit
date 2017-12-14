# Form file list 

html: 
```
<div class="form-row file-row js-file-row">
    <label class="reviewFile" for="reviewFile"><span class="file-label-text">Прикрепить фотографию</span><span class="tooltip js-tooltip"><span>Допустимые форматы: .jpeg, .png. Размер: не&nbsp;более 10&nbsp;МБ</span></span></label>

    <div class="input-row">
        <input type="file" name="reviewFile[]" id="reviewFile" class="form-control" data-rule-extension="jpg|png|jpeg" data-msg-accept="Загрузите изображение jpeg/png" data-rule-filesize="10485760" data-msg-filesize="Максимальный размер 10Мб" multiple="multiple" accept="image/jpeg,image/png">
        <div class="file-list js-file-list">
            <a href="javascript:void(0);" class="js-file-preview" data-name="cart_empty.png" >
                <span class="img" style="background-image: url()"></span>
            </a>

            <a href="javascript:void(0);" class="js-file-preview" data-name="cart_empty.png" >
                <span class="img" style="background-image: url()"></span>
            </a>
        </div>
    </div>
</div>
```

style: 
```

.file-row {
    label {
        .hover-opacity();
        position: relative;
        padding-left: 24px;
        cursor: pointer;

        &:before {
            .pseudo();
            .icon(@file);
            left: 0;
            top: 2px;
        }

        &:focus,
        &:hover {
            opacity: 1;

            .file-label-text {
                border-bottom-color: transparent;
            }
        }
    }

    .file-label-text {
        .transit(border-bottom);
        .font(15px);
        color: @cPrimary;
        border-bottom: 1px dotted currentColor;
    }

    .file-list {
        margin-top: 15px;
        font-size: 0;

        &:empty {
            padding-bottom: 1px;
        }

        a {
            .size(40px);
            position: relative;
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
            margin-bottom: 10px;

            &:before,
            &:after {
                .transit(opacity);
                .pseudo();
                position: absolute;
                opacity: 0;
                z-index: 1;
            }

            &:before {
                .size(16px, 18px);
                top: 0;
                right: 0;
                background-color: @cPrimary;
            }

            &:after {
                .icon(@cross);
                top: 6px;
                right: 5px;
            }

            &:hover {
                opacity: 1;

                &:before,
                &:after {
                    opacity: 1;
                }

                .img {
                    opacity: .55;
                }
            }
        }

        .img {
            .size(100%);
            .transit(opacity);
            display: block;
            background: center no-repeat;
            background-size: cover;
        }
    }
}
    
@media @media-lg {
    input[type=file] {
        border: 0;
        clip: rect(0 0 0 0);
        clip-path: inset(50%);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
        white-space: nowrap;
    }
}

@media @media-xs, @media-sm, @media-md {
    input[type=file] {
        position: absolute;
        top: -37px;
        z-index: 1;
        opacity: 0;
    }
}
```